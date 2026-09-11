<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Selection multiple</title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        select {
            width: 100%;
            margin-top: 10px;
            padding-top: 5px;
        }

        label {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 20px;
            padding-bottom: 10px;            
        }

        body {
            padding: 5px;
            padding-right: 10px;
        }

        /* Taille du Select2 */ 
        .select2-container { width: 100% !important; margin-top: 10px; } .select2-container .select2-selection--multiple { min-height: 40px; border: 1px solid #ccc; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="champ">
        <label for="grade">Grades</label>                
        <select name="grade" id="grade" multiple>                                
            <option value="">Tous</option>
            <option value="13">CC1</option>
            <option value="14">CC2</option>
            <option value="15">CC3</option>
            <option value="16">S1</option>
            <option value="07">Q1</option>
            <option value="08">Q2</option>
            <option value="09">HQ</option>
        </select>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script> $(document).ready(function () { 
        $('#grade').select2({ placeholder: 'Sélectionner un ou plusieurs grades', allowClear: true, width: '100%' }); 
        }); 
    </script>
</body>
</html>