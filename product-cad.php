<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}

$user_id = $_SESSION['user_id'];

require 'conn.php';

$sql = "SELECT * FROM user WHERE userId = $user_id";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row['name'];
    $surname = $row['surname'];
    $roleId = $row['roleId'];
}

$sql = "SELECT * FROM role WHERE roleId = $roleId";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $roleName = $row['roleName'];
}

if (isset($_GET['productId'])) {
    $productId = intval($_GET['productId']);

    $sqlProductInfo = "SELECT * FROM product WHERE productId = $productId";
    $resultProductInfo = $conn->query($sqlProductInfo);
    if ($resultProductInfo->num_rows > 0) {
        $row = $resultProductInfo->fetch_assoc();
        $productName = $row['productName'];
        $ticketId = $row['ticketId'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lines = json_decode($_POST['lines'], true);
    $texts = json_decode($_POST['texts'], true);

    $conn->query("DELETE FROM lines WHERE productId = $productId");
    $conn->query("DELETE FROM texts WHERE productId = $productId");

    $stmt = $conn->prepare("INSERT INTO lines (productId, x1, y1, x2, y2, isArc, isArrow, number) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($lines as $line) {
        $stmt->bind_param('iiiiiiis', $productId, $line['x1'], $line['y1'], $line['x2'], $line['y2'], $line['isArc'], $line['isArrow'], $line['number']);
        $stmt->execute();
    }
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO texts (productId, text, x, y) VALUES (?, ?, ?, ?)");
    foreach ($texts as $text) {
        $stmt->bind_param('issi', $productId, $text['text'], $text['x'], $text['y']);
        $stmt->execute();
    }
    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="/assets/favicon/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/style.css?<?php echo time(); ?>">
    <link rel="stylesheet" href="css/product-cad.css?<?php echo time(); ?>">
    <title>Чертёж №1</title>
</head>
<body>
    <canvas></canvas>
    <div class="nav">
        <p class="title"><a href="ticket?ticketId=<?php echo $ticketId;?>"><</a><input type="text" name="productName" id="productName" value="<?php echo $productName;?>"></p>
        <hr>
        <p class="layers">Шаблоны</p>
        <div class="layers">
            <div class="empty">
                <img src="" alt="">
                <p class="title">Недоступно</p>
            </div>
        </div>
    </div>

    <div class="tools">
        <button id="textButton" type="button">Т</button>
        <button id="lineButton" type="button">У</button>
        <button id="anotherButton" type="button">Л</button>
        <button id="anotherButton2" type="button">З</button>
    </div>

    <div class="undo-tools">
        <button type="button"><</button>
        <button type="button">></button>
    </div>

    <script>
        const canvas = document.querySelector('canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        const gridColor = '#E0E4E8';
        const lineColor = '#3C3C3C';
        const gridSize = 20;
        const arrowSize = 10;

        let lines = [];
        let texts = [];

        let editingNumber = false;

        let isDrawing = false;
        let isTextMode = false;
        let isArrowMode = false;
        let isArcMode = false;
        let isEraserMode = false;

        let startX, startY, currentX, currentY;

        function drawGrid() {
            ctx.strokeStyle = gridColor;
            ctx.lineWidth = 0.5;
            
            for (let x = 0; x <= canvas.width; x += gridSize) {
                ctx.beginPath();
                ctx.moveTo(x, 0);
                ctx.lineTo(x, canvas.height);
                ctx.stroke();
            }
            
            for (let y = 0; y <= canvas.height; y += gridSize) {
                ctx.beginPath();
                ctx.moveTo(0, y);
                ctx.lineTo(canvas.width, y);
                ctx.stroke();
            }
        }

        function drawArrow(x1, y1, x2, y2) {
            const angle = Math.atan2(y2 - y1, x2 - x1);
            ctx.beginPath();
            ctx.moveTo(x2, y2);
            ctx.lineTo(x2 - arrowSize * Math.cos(angle - Math.PI / 6), y2 - arrowSize * Math.sin(angle - Math.PI / 6));
            ctx.lineTo(x2 - arrowSize * Math.cos(angle + Math.PI / 6), y2 - arrowSize * Math.sin(angle + Math.PI / 6));
            ctx.lineTo(x2, y2);
            ctx.fillStyle = lineColor;
            ctx.fill();
        }

        function drawArc(x1, y1, x2, y2) {
            x1 = Math.round(x1 / gridSize) * gridSize;
            y1 = Math.round(y1 / gridSize) * gridSize;
            x2 = Math.round(x2 / gridSize) * gridSize;
            y2 = Math.round(y2 / gridSize) * gridSize;

            const radius = Math.hypot(x2 - x1, y2 - y1) / 2;
            const centerX = (x1 + x2) / 2;
            const centerY = (y1 + y2) / 2;

            const angleX = x2 - x1;
            const angleY = y2 - y1;

            ctx.beginPath();

            if (Math.abs(angleX) > Math.abs(angleY)) {
                if (x2 > x1) {
                    ctx.arc(centerX, centerY, radius, Math.PI, 2 * Math.PI, false);
                } else {
                    ctx.arc(centerX, centerY, radius, 0, Math.PI, false);
                }
            } else {
                if (y2 > y1) {
                    ctx.arc(centerX, centerY, radius, 1.5 * Math.PI, 0.5 * Math.PI, false);
                } else {
                    ctx.arc(centerX, centerY, radius, 0.5 * Math.PI, 1.5 * Math.PI, false);
                }
            }

            ctx.stroke();
        }

        function drawAll() {
            clearCanvas();
            ctx.strokeStyle = lineColor;
            ctx.lineWidth = 2;

            lines.forEach(line => {
                console.log(`Drawing line: ${line.isArc ? 'Arc' : 'Line'}, isArrow: ${line.isArrow}, Coordinates: (${line.x1}, ${line.y1}) to (${line.x2}, ${line.y2}), Number: ${line.number}`);

                if (line.isArc) {
                    console.log('Drawing Arc');
                    drawArc(line.x1, line.y1, line.x2, line.y2);
                } else {
                    console.log('Drawing Line');
                    ctx.beginPath();
                    ctx.moveTo(line.x1, line.y1);
                    ctx.lineTo(line.x2, line.y2);
                    ctx.stroke();

                    if (line.isArrow) {
                        console.log('Drawing Arrow');
                        drawArrow(line.x1, line.y1, line.x2, line.y2);
                    } 

                    const midX = (line.x1 + line.x2) / 2;
                    const midY = (line.y1 + line.y2) / 2;
                    const offset = 10;
                    ctx.font = '16px Arial';
                    ctx.fillStyle = 'black';
                    
                    if (line.number && line.number !== '') {
                        console.log(`Drawing Line Number: ${line.number}`);
                        ctx.fillText(line.number, midX + offset, midY - offset);
                    } else {
                        console.log('Line number is missing');
                    }
                }
            });

            texts.forEach(text => {
                console.log(`Drawing Text: ${text.text} at (${text.x}, ${text.y})`);
                ctx.font = '20px Inter';
                ctx.fillStyle = 'black';
                ctx.fillText(text.text, text.x, text.y);
            });

            saveToDatabase();
        }



        function clearCanvas() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            drawGrid();
        }

        function eraseElement(mouseX, mouseY) {
            lines = lines.filter(line => {
                const midX = (line.x1 + line.x2) / 2;
                const midY = (line.y1 + line.y2) / 2;
                const offset = 20; 
                const withinLine = Math.abs(mouseX - (midX + offset)) > 20 || Math.abs(mouseY - (midY + offset)) > 20;
                return withinLine;
            });

            texts = texts.filter(text => {
                return Math.abs(mouseX - text.x) > 20 || Math.abs(mouseY - text.y) > 20;
            });

            drawAll();
        }

        const textButton = document.getElementById('textButton');
        const lineButton = document.getElementById('lineButton');
        const anotherButton = document.getElementById('anotherButton');
        const anotherButton2 = document.getElementById('anotherButton2');

        canvas.addEventListener('mousemove', (e) => {
            if (isDrawing) {
                currentX = Math.round(e.clientX / gridSize) * gridSize;
                currentY = Math.round(e.clientY / gridSize) * gridSize;

                drawAll(); 

                ctx.strokeStyle = lineColor;
                ctx.lineWidth = 2;

                if (isArcMode) {
                    drawArc(startX, startY, currentX, currentY);
                } else {
                    ctx.beginPath();
                    ctx.moveTo(startX, startY);
                    ctx.lineTo(currentX, currentY);
                    ctx.stroke(); 
                    if (isArrowMode) {
                        drawArrow(startX, startY, currentX, currentY);
                    }
                }
            }
        });

        canvas.addEventListener('mousedown', (e) => {
            if (isEraserMode) {
                eraseElement(e.clientX, e.clientY); 
            } else if (!editingNumber && !isTextMode) {
                isDrawing = true;
                startX = Math.round(e.clientX / gridSize) * gridSize;
                startY = Math.round(e.clientY / gridSize) * gridSize;
                currentX = startX;
                currentY = startY;
            }
            editingNumber = false; 
        });

        canvas.addEventListener('mouseup', (e) => {
            if (isDrawing) {
                const endX = Math.round(e.clientX / gridSize) * gridSize;
                const endY = Math.round(e.clientY / gridSize) * gridSize;

                if (startX !== endX || startY !== endY) {
                    if (isArcMode) {
                        lines.push({ x1: startX, y1: startY, x2: endX, y2: endY, isArc: true });
                    } else if (isArrowMode) {
                        lines.push({ x1: startX, y1: startY, x2: endX, y2: endY, number: '', isArrow: true });
                    } else {
                        lines.push({ x1: startX, y1: startY, x2: endX, y2: endY, number: '100', isArrow: false });
                    }
                }

                drawAll();
            }
            isDrawing = false;
        });

        canvas.addEventListener('click', (e) => {
            const mouseX = e.clientX;
            const mouseY = e.clientY;

            lines.forEach((line, index) => {
                const midX = (line.x1 + line.x2) / 2;
                const midY = (line.y1 + line.y2) / 2;
                const offset = 20; 

                if (!line.isArrow && !line.isArc && Math.abs(mouseX - (midX + offset)) < 20 && Math.abs(mouseY - (midY + offset)) < 20) {
                    const newNumber = prompt('Введите новое значение для линии:', line.number);
                    if (newNumber !== null) {
                        line.number = newNumber;
                        drawAll();
                    }
                    editingNumber = true; 
                }
            });

            if (isTextMode) {
                const text = prompt('Введите текст:');
                if (text) {
                    const x = e.clientX;
                    const y = e.clientY;
                    texts.push({ text, x, y });
                    drawAll();
                    setActiveButton(textButton); 
                }
            }
        });

        function setActiveButton(button) {
            document.querySelectorAll('.tools button').forEach(btn => btn.classList.remove('active'));

            if (button === textButton) {
                isTextMode = !isTextMode;
                isArrowMode = false;
                isArcMode = false;
                isEraserMode = false;
            } else if (button === lineButton) {
                isArrowMode = !isArrowMode;
                isTextMode = false;
                isArcMode = false;
                isEraserMode = false;
            } else if (button === anotherButton) {
                isEraserMode = !isEraserMode;
                isTextMode = false;
                isArrowMode = false;
                isArcMode = false;
            } else if (button === anotherButton2) {
                isArcMode = !isArcMode;
                isTextMode = false;
                isArrowMode = false;
                isEraserMode = false;
            }

            if (isTextMode || isArrowMode || isArcMode || isEraserMode) {
                button.classList.add('active');
            }
        }

        textButton.addEventListener('click', () => setActiveButton(textButton));
        lineButton.addEventListener('click', () => setActiveButton(lineButton));
        anotherButton.addEventListener('click', () => setActiveButton(anotherButton)); 
        anotherButton2.addEventListener('click', () => setActiveButton(anotherButton2));

        drawGrid();

        window.onload = function() {
            fetch(`load?productId=${<?php echo $productId; ?>}`)
                .then(response => response.json())
                .then(data => {
                    lines = data.lines.map(line => ({
                        ...line,
                        isArc: line.isArc === "1",
                        isArrow: line.isArrow === "1"
                    }));
                    texts = data.texts;
                    console.log(data)
                    drawAll();
                })
                .catch(error => console.error('Error loading data:', error));
        };

        function saveToDatabase() {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'save', true);
            xhr.setRequestHeader('Content-Type', 'application/json');

            const data = {
                productId: <?php echo $productId; ?>,
                lines: lines,
                texts: texts
            };

            console.log('Data to be sent:', JSON.stringify(data));

            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 300) {
                    console.log('Success:', xhr.responseText);
                } else {
                    console.error('Error:', xhr.statusText);
                }
            };

            xhr.onerror = function() {
                console.error('Request failed');
            };

            xhr.send(JSON.stringify(data));
        }
    </script>
</body>
</html>