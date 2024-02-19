<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Looping</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 20px;
        }
        .output {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .button-container {
            text-align: center;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="output" id="output"></div>

<div class="button-container">
    <button id="nextButton">Next</button>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let outputDiv = document.getElementById('output');
        let nextButton = document.getElementById('nextButton');
        let outputContent = {!! json_encode($output) !!};
        let outputArray = outputContent.split('<br>');

        let currentIndex = 0;

        function displayNext() {
            if (currentIndex < outputArray.length) {
                outputDiv.innerHTML += outputArray[currentIndex] + '<br>';
                currentIndex++;
            }
        }

        nextButton.addEventListener("click", function() {
            displayNext();
        });
    });
</script>
</body>
</html>
