<?php
// Simple Command Processing Web Application
/**
 * Instructions
 * --- Enter numbers or Float values on the input field.
 * --- Press spacebar or Click "Add Input" to add more input fields.
 * --- Click Any Button to execute the command
 * --- Click "Clear" to clear all inputs
 * --- Click Repo Description button to get repo description
 * */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'process/controllers/src/execute.php';

    $command = $_POST['command'] ?? '';
    echo json_encode(["result" => executeCommand($command)]);
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Command App</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="container mt-5">
    <div class="row">
        <div class="col-lg-8">
            <div class="input-container">
                <h2>Command Input</h2>
                <div id="inputs">
                    <input type="text" class="form-control number-input" placeholder="Enter number">
                </div>
                <button id="add-input" class="btn btn-secondary mt-2">Add Input</button>
                <button id="clear-inputs" class="btn btn-danger mt-2">Clear</button>
                <div class="mt-3">
                    <button class="btn btn-primary calc-btn" data-operation="sum">Sum</button>
                    <button class="btn btn-primary calc-btn" data-operation="difference">Difference</button>
                    <button class="btn btn-primary calc-btn" data-operation="product">Product</button>
                    <button class="btn btn-primary calc-btn" data-operation="quotient">Quotient</button>
                </div>
                <div class="mt-3">
                    <button class="btn btn-primary repo-btn" data-operation="repo-desc">Get Repo Description</button>
                </div>
            </div>
        </div>
        <div class="col-lg-4 border border-dark">
            <div class="result-container">
                <h3 class="mt-4 py-3">Result:
                    <span id="result"> </span>
                </h3>
                <pre id="repo-result"> </pre>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            function addInputField() {
                let newInput = $('<input type="text" class="form-control number-input mt-2" placeholder="Enter number">');
                $('#inputs').append(newInput);
                newInput.focus();
            }

            $('#add-input').click(function(){
                addInputField();
            });

            $('#clear-inputs').click(function(){
                $('#inputs').html('<input type="text" class="form-control number-input" placeholder="Enter number">');
                $('#result').text('');
                $('#repo-result').text('');
            });

            $(document).on('keydown', '.number-input', function(event) {
                if (event.key === ' ') {
                    event.preventDefault();
                    addInputField();
                }
            });

            $(document).on('input', '.number-input', function() {
                $(this).val($(this).val().replace(/[^0-9.]/g, ''));
            });

            $('.calc-btn').click(function(){
                let operation = $(this).data('operation');
                let numbers = [];
                $('.number-input').each(function(){
                    let val = $(this).val();
                    if (val !== '') numbers.push(val);
                });

                $('#result').text('Please wait...');
                $('#repo-result').text('');
                $.post('index.php', { command: operation + ' ' + numbers.join(' ') }, function(data){
                    $('#result').text(JSON.stringify(data.result, null, 2));
                }, 'json');
            });

            $('.repo-btn').click(function(){
                let ownerRepo = prompt("Enter GitHub owner/repo (e.g. google/WebFundamentals)");
                if (ownerRepo) {
                    $('#result').text('');
                    $('#repo-result').text('Please wait...');
                    $.post('index.php', { command: 'repo-desc ' + ownerRepo }, function(data){
                        if (data.result.isText) {
                            $('#repo-result').text(data.result.description);
                        } else {
                            $('#result').text(JSON.stringify(data.result, null, 2));
                        }
                    }, 'json');
                }
            });
        });
    </script>
</body>
</html>
