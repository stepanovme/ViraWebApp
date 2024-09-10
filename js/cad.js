const canvas = document.querySelector('canvas');
const ctx = canvas.getContext('2d');
canvas.width = window.innerWidth;
canvas.height = window.innerHeight;

// Grid settings
const gridColor = '#E0E4E8';
const lineColor = '#3C3C3C';
const gridSize = 20;
const arrowSize = 10; // Size of arrow

// Arrays to store all drawn lines, text, and arcs
let lines = [];
let texts = [];
let arcs = [];

// Track whether we are interacting with a number (to prevent drawing lines)
let editingNumber = false;

// Track if the text mode, line mode, or arc mode is active
let isTextMode = false;
let isArrowMode = false;
let isArcMode = false;
let arcDirection = 'right'; // Default arc direction
let isDrawing = false;
let startX, startY, currentX, currentY;

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
function drawArc(x1, y1, x2, y2, direction) {
    const radius = Math.sqrt(Math.pow(x2 - x1, 2) + Math.pow(y2 - y1, 2)) / 2;
    const centerX = (x1 + x2) / 2;
    const centerY = (y1 + y2) / 2;
    const startAngle = Math.atan2(y1 - centerY, x1 - centerX);
    const endAngle = Math.atan2(y2 - centerY, x2 - centerX);

    ctx.beginPath();
    ctx.strokeStyle = lineColor;
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';

    switch (direction) {
        case 'right':
            ctx.arc(centerX - radius, centerY, radius, startAngle, endAngle);
            break;
        case 'left':
            ctx.arc(centerX + radius, centerY, radius, endAngle, startAngle);
            break;
        case 'down':
            ctx.arc(centerX, centerY - radius, radius, startAngle, endAngle);
            break;
        case 'up':
            ctx.arc(centerX, centerY + radius, radius, endAngle, startAngle);
            break;
    }

    ctx.stroke();
}

// Function to draw all lines, texts, and arcs
function drawAll() {
    clearCanvas(); // Clear the canvas and redraw the grid
    ctx.strokeStyle = lineColor;
    ctx.lineWidth = 2;

    lines.forEach(line => {
        // Draw the line
        ctx.beginPath();
        ctx.moveTo(line.x1, line.y1);
        ctx.lineTo(line.x2, line.y2);
        ctx.stroke();

        // Draw the arrow if needed
        if (line.isArrow) {
            drawArrow(line.x1, line.y1, line.x2, line.y2);
        } else {
            // Draw the number near the line but offset it
            const midX = (line.x1 + line.x2) / 2;
            const midY = (line.y1 + line.y2) / 2;
            const offset = 20; // Offset from the line
            ctx.font = '16px Arial';
            ctx.fillStyle = 'black';
            ctx.fillText(line.number, midX + offset, midY + offset); // Position the number away from the line
        }
    });

    texts.forEach(text => {
        ctx.font = '20px Arial';
        ctx.fillStyle = 'black';
        ctx.fillText(text.text, text.x, text.y);
    });

    arcs.forEach(arc => {
        drawArc(arc.x1, arc.y1, arc.x2, arc.y2, arc.direction);
    });
}

// Function to clear canvas and redraw the grid
function clearCanvas() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    drawGrid();
}

// Function to show the preview line
canvas.addEventListener('mousemove', (e) => {
    if (isDrawing) {
        currentX = Math.round(e.clientX / gridSize) * gridSize;
        currentY = Math.round(e.clientY / gridSize) * gridSize;

        drawAll(); // Redraw all existing lines, texts, and arcs
        ctx.strokeStyle = lineColor;
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.moveTo(startX, startY);
        ctx.lineTo(currentX, currentY);
        ctx.stroke(); // Draw preview of the current line
        if (isArrowMode) {
            drawArrow(startX, startY, currentX, currentY);
        }
        if (isArcMode) {
            drawArc(startX, startY, currentX, currentY, arcDirection);
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

// Function to stop drawing and finalize the shape
canvas.addEventListener('mouseup', (e) => {
    if (isDrawing) {
        const endX = Math.round(e.clientX / gridSize) * gridSize;
        const endY = Math.round(e.clientY / gridSize) * gridSize;

        // Add a line only if start and end points are different
        if (startX !== endX || startY !== endY) {
            if (isArrowMode) {
                lines.push({ x1: startX, y1: startY, x2: endX, y2: endY, number: '', isArrow: true });
            } else if (isArcMode) {
                arcs.push({ x1: startX, y1: startY, x2: endX, y2: endY, direction: arcDirection });
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
        if (!line.isArrow && Math.abs(mouseX - (midX + offset)) < 20 && Math.abs(mouseY - (midY + offset)) < 20) {
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
const arcButton = document.getElementById('arcButton');
const anotherButton = document.getElementById('anotherButton');
const anotherButton2 = document.getElementById('anotherButton2');

function setActiveButton(button) {
    document.querySelectorAll('.tools button').forEach(btn => btn.classList.remove('active'));
    if (button === textButton && isTextMode) {
        isTextMode = false;
    } else {
        button.classList.add('active');
        isTextMode = button === textButton;
        isArrowMode = button === lineButton;
        isArcMode = button === arcButton;
    }
}

textButton.addEventListener('click', () => setActiveButton(textButton));
lineButton.addEventListener('click', () => setActiveButton(lineButton));
arcButton.addEventListener('click', () => setActiveButton(arcButton));
anotherButton.addEventListener('click', () => setActiveButton(anotherButton));
anotherButton2.addEventListener('click', () => setActiveButton(anotherButton2));

// Function to handle arc direction buttons
const rightArcButton = document.getElementById('rightArcButton');
const leftArcButton = document.getElementById('leftArcButton');
const downArcButton = document.getElementById('downArcButton');
const upArcButton = document.getElementById('upArcButton');

rightArcButton.addEventListener('click', () => { arcDirection = 'right'; setActiveButton(arcButton); });
leftArcButton.addEventListener('click', () => { arcDirection = 'left'; setActiveButton(arcButton); });
downArcButton.addEventListener('click', () => { arcDirection = 'down'; setActiveButton(arcButton); });
upArcButton.addEventListener('click', () => { arcDirection = 'up'; setActiveButton(arcButton); });

// Resize canvas and redraw grid and lines when the window size changes
window.addEventListener('resize', () => {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    drawAll(); // Redraw everything on resize
});

// Draw the initial grid
drawGrid();
