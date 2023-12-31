<!doctype html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>HTML Sample</title>
    <style>
        .container {
            display: flex;
            align-items: center;
        }

        .buttons {
            display: flex;
            flex-direction: column;
            margin-left: 10px;
        }

        .container>div,
        .buttons>button {
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div id="editor" name="md_src" style="width:800px;height:600px;border:1px solid grey"></div>
        <div id="preview" name="md_src" style="width:800px;height:600px;border:1px solid grey">
            <img id="editor-image" src="" alt=" ">
        </div>
        <div id="answer" name="md_src" style="width:800px;height:600px;border:1px solid grey">
            <img id="answer-image" src="<?php echo htmlspecialchars($b64EncodedAnswerPNG); ?>" alt=" ">
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.20.0/min/vs/loader.min.js"></script>
    <script>
        require.config({
            paths: {
                'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.20.0/min/vs'
            }
        });
        require(['vs/editor/editor.main'], function() {
            window.editor = monaco.editor.create(document.getElementById('editor'), {
                value: '',
                language: 'markdown'
            });
            window.editor.onDidChangeModelContent(displayFigure);
        });
        async function displayFigure() {
            try {
                let plantUmlText = window.editor.getValue();
                let param = {
                    'action': 'display',
                    'uml': plantUmlText
                };
                let response = await executeApi(param);
                let b64EncodedPNG = await response.text();
                document.getElementById('editor-image').src = b64EncodedPNG;
            } catch (error) {
                console.error('Error:', error);
            }
        }
        async function executeApi(paramMapping) {
            try {
                var formBody = [];
                for (var property in paramMapping) {
                    var encodedKey = encodeURIComponent(property);
                    var encodedValue = encodeURIComponent(paramMapping[property]);
                    formBody.push(encodedKey + "=" + encodedValue);
                }
                formBody = formBody.join("&");
                const response = await fetch('http://localhost:8081', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: formBody
                });
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                console.log(response);
                return response;
            } catch (error) {
                console.error('Error:', error);
            }
        }
    </script>
</body>

</html>