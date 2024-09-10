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
        <p class="title"><a href=""><</a>Откосная планка 300мм</p>
        <hr>
        <p class="layers">Слои</p>
        <div class="layers">
            <p>Линия</p>
            <p>Линия</p>
            <p>Линия</p>
            <p>Линия</p>
            <p>Линия</p>
            <p>Линия</p>
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

        // Grid settings
        const gridColor = '#E0E4E8';
        const lineColor = '#3C3C3C';
        const gridSize = 20;
        const arrowSize = 10; // Размер стрелки

        // Arrays to store all drawn lines and text
        let lines = [];
        let texts = [];

        // Track whether we are interacting with a number (to prevent drawing lines)
        let editingNumber = false;

        // Track if the text mode, arrow mode, or arc mode is active
        let isTextMode = false;
        let isArrowMode = false;
        let isArcMode = false;

        // Function to draw the grid
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

        // Function to draw an arrow
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

        // Function to draw an arc
        function drawArc(x1, y1, x2, y2) {
            // Привязываем координаты начала и конца дуги к сетке
            x1 = Math.round(x1 / gridSize) * gridSize;
            y1 = Math.round(y1 / gridSize) * gridSize;
            x2 = Math.round(x2 / gridSize) * gridSize;
            y2 = Math.round(y2 / gridSize) * gridSize;

            const radius = Math.hypot(x2 - x1, y2 - y1) / 2; // Радиус — половина расстояния между точками
            const centerX = (x1 + x2) / 2; // Центр дуги по X
            const centerY = (y1 + y2) / 2; // Центр дуги по Y

            const angleX = x2 - x1;
            const angleY = y2 - y1;

            ctx.beginPath();

            if (Math.abs(angleX) > Math.abs(angleY)) {
                // Горизонтальная дуга (слева направо или справа налево)
                if (x2 > x1) {
                    // Справа направо — нижняя дуга
                    ctx.arc(centerX, centerY, radius, Math.PI, 2 * Math.PI, false); // Полуокружность вниз
                } else {
                    // Слева направо — верхняя дуга
                    ctx.arc(centerX, centerY, radius, 0, Math.PI, false); // Полуокружность вверх
                }
            } else {
                // Вертикальная дуга (сверху вниз или снизу вверх)
                if (y2 > y1) {
                    // Сверху вниз — правая дуга
                    ctx.arc(centerX, centerY, radius, 1.5 * Math.PI, 0.5 * Math.PI, false); // Полуокружность вправо
                } else {
                    // Снизу вверх — левая дуга
                    ctx.arc(centerX, centerY, radius, 0.5 * Math.PI, 1.5 * Math.PI, false); // Полуокружность влево
                }
            }

            ctx.stroke();
        }

        // Function to draw all lines and texts
        function drawAll() {
            clearCanvas(); // Clear the canvas and redraw the grid
            ctx.strokeStyle = lineColor;
            ctx.lineWidth = 2;

            lines.forEach(line => {
                if (line.isArc) {
                    // Draw arc
                    drawArc(line.x1, line.y1, line.x2, line.y2);
                } else {
                    // Draw line
                    ctx.beginPath();
                    ctx.moveTo(line.x1, line.y1);
                    ctx.lineTo(line.x2, line.y2);
                    ctx.stroke();

                    // Draw arrow if needed
                    if (line.isArrow) {
                        drawArrow(line.x1, line.y1, line.x2, line.y2);
                    } else {
                        // Draw number near the line but offset it
                        const midX = (line.x1 + line.x2) / 2;
                        const midY = (line.y1 + line.y2) / 2;
                        const offset = 20; // Offset from the line
                        ctx.font = '16px Arial';
                        ctx.fillStyle = 'black';
                        ctx.fillText(line.number, midX + offset, midY + offset); // Position the number away from the line
                    }
                }
            });

            texts.forEach(text => {
                ctx.font = '20px Arial';
                ctx.fillStyle = 'black';
                ctx.fillText(text.text, text.x, text.y);
            });
        }

        // Function to clear canvas and redraw the grid
        function clearCanvas() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            drawGrid();
        }

        // Function to show the preview line or arc
        canvas.addEventListener('mousemove', (e) => {
            if (isDrawing) {
                currentX = Math.round(e.clientX / gridSize) * gridSize;
                currentY = Math.round(e.clientY / gridSize) * gridSize;

                drawAll(); // Redraw all existing lines and texts

                ctx.strokeStyle = lineColor;
                ctx.lineWidth = 2;

                if (isArcMode) {
                    drawArc(startX, startY, currentX, currentY); // Draw arc preview
                } else {
                    ctx.beginPath();
                    ctx.moveTo(startX, startY);
                    ctx.lineTo(currentX, currentY);
                    ctx.stroke(); // Draw preview of the current line
                    if (isArrowMode) {
                        drawArrow(startX, startY, currentX, currentY);
                    }
                }
            }
        });

        // Function to start drawing
        canvas.addEventListener('mousedown', (e) => {
            if (!editingNumber && !isTextMode) {
                isDrawing = true;
                startX = Math.round(e.clientX / gridSize) * gridSize;
                startY = Math.round(e.clientY / gridSize) * gridSize;
                currentX = startX;
                currentY = startY;
            }
            editingNumber = false; // Reset after possible number editing
        });

        // Function to stop drawing and finalize the line or arc
        canvas.addEventListener('mouseup', (e) => {
            if (isDrawing) {
                const endX = Math.round(e.clientX / gridSize) * gridSize;
                const endY = Math.round(e.clientY / gridSize) * gridSize;

                if (startX !== endX || startY !== endY) {
                    if (isArcMode) {
                        // Add an arc
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

        // Function to edit the number when clicking on it
        canvas.addEventListener('click', (e) => {
            const mouseX = e.clientX;
            const mouseY = e.clientY;

            lines.forEach((line, index) => {
                const midX = (line.x1 + line.x2) / 2;
                const midY = (line.y1 + line.y2) / 2;
                const offset = 20; // Offset for the number

                // Check if the click is near the text (number)
                if (!line.isArrow && !line.isArc && Math.abs(mouseX - (midX + offset)) < 20 && Math.abs(mouseY - (midY + offset)) < 20) {
                    const newNumber = prompt('Введите новое значение для линии:', line.number);
                    if (newNumber !== null) {
                        line.number = newNumber;
                        drawAll();
                    }
                    editingNumber = true; // Mark that we edited a number to prevent drawing a new line
                }
            });

            if (isTextMode) {
                const text = prompt('Введите текст:');
                if (text) {
                    const x = e.clientX;
                    const y = e.clientY;
                    texts.push({ text, x, y });
                    drawAll();
                    setActiveButton(textButton); // Deactivate text mode after adding text
                }
            }
        });

        // Function to handle tool buttons
        const textButton = document.getElementById('textButton');
        const lineButton = document.getElementById('lineButton');
        const anotherButton = document.getElementById('anotherButton');
        const anotherButton2 = document.getElementById('anotherButton2');

        function setActiveButton(button) {
            document.querySelectorAll('.tools button').forEach(btn => btn.classList.remove('active'));
            if ((button === textButton && isTextMode) || (button === lineButton && isArrowMode) || (button === anotherButton) || (button === anotherButton2 && isArcMode)) {
                isTextMode = false;
                isArrowMode = false;
                isArcMode = false;
            } else {
                button.classList.add('active');
                isTextMode = button === textButton;
                isArrowMode = button === lineButton;
                isArcMode = button === anotherButton2;
            }
        }

        textButton.addEventListener('click', () => setActiveButton(textButton));
        lineButton.addEventListener('click', () => setActiveButton(lineButton));
        anotherButton.addEventListener('click', () => setActiveButton(anotherButton));
        anotherButton2.addEventListener('click', () => setActiveButton(anotherButton2));

        // Resize canvas and redraw grid on window resize
        window.addEventListener('resize', () => {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
            drawAll();
        });

        // Initial setup
        drawGrid();



    </script>
</body>
</html>