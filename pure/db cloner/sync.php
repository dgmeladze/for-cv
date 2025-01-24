<html>
    <head>
        <script src="/catalog/view/javascript/jquery/jquery-2.1.1.min.js"></script>
        <style>
            input {
                margin:5px;
                padding:10px;
                font-size:16px;
            }
            #synctables {
                display:flex;
                flex-wrap: wrap;
            }
            button {
                margin:5px;
                border:1px solid #000;
                font-size:16px;
                color:#fff;
                display:flex;
                align-items: center;
                justify-content: center;
                background: #000;
                padding:30px 20px;
                font-weight:bold;
                cursor:pointer;
            }
            button:active, button:focus {
                background:#fff;
                color:#000;
            }
            </style>
    </head>
    <body>
        <div id="synctables">
            <input type="hidden" value="table_1" />
            <input type="hidden" value="table_2" />
            <input type="hidden" value="table_3" />
            <input type="hidden" value="table_4" />
            <input type="hidden" value="table_5" />
            <input type="hidden" value="table_6" />
            <input type="hidden" value="table_7" />
            <input type="hidden" value="table_8" />
            <input type="hidden" value="table_9" />
            <input type="hidden" value="table_10" />
    </div>
    <div id="buttons"> 
        <button onclick="syncLoop(0);">Синхронизация базы данных</button>
    </div>
    <div id="buttons"> 
        <button onclick="syncImage();">Синхронизация изображений</button>
    </div>
    <h2>Инструкция</h2>
    <div id="description">
        <p>После обноления информации на основном сайте вы можете легко обновить и на этом при помощи этого инструмента.<br />
        Кнопка "Синхронизация базы данных" обновляет информацию о товаре, а так же выгружает в каталог главное изображение товара. <br />
        Кнопка "Синхронизация изображений" выгружает изображения из базы данных, проверяет - есть ли такое изображение в каталоге этого сайта, есть ли путь до нее. Если нет, то создает путь, создает картинку. Если же изображение с таким путем имеется, то ничего не происходит. Данную функцию рекомендуется запустить 2-3 раза с интервалом не менее получаса, можно и на след. день, так как за 1 раз может просто не хватить мощности хостинга .
        <br/>
        ВАЖНО!!!<br />
        Синхронизация баз данных занимает где то 2-3 минуты, пока браузер не выдаст вам оповещение, советуется НЕ ЗАКРЫВАТЬ СТРАНИЦУ.<br />
        Синхронизация изображений занимает больше времени. Просто так же оставьте вкладку включенной минут на 10.<br />
        Крайне не рекомендуется запускать обе функции сразу, так как это может вызвать большую нагрузку на хостинг.
    </div>
    </body>
    <script>
        let i = 1;
        function syncLoop(l) {
            // Не люблю if, но иногда для ускорения процесса использую.
            if (i <= $('#synctables input').length) {
                
                $.ajax({
                    url: 'sync_tables.php',
                    type: 'post',
                    data: 'table=' + $('#synctables input:nth-child(' + i + ')').val(),
                    dataType: 'json',
                    success: function(json) {
                        if (json['success']) {
                            $(this).text('Готово');
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        
                    }
                });
                $('#synctables input:nth-child(1)').css('background', 'green')
                setTimeout(function() {
                    i++;
                    console.log(i);
                    $('#synctables input:nth-child(' + l + ')').css('background', 'green');
                    syncLoop(i);
                }, 5000);
            } else {
                alert('Выполнено');
            }
        }
        function syncImage() {             
                $.ajax({
                    url: 'syncimage.php',
                    type: 'post',
                    data: 'table=' + $('#synctables input:nth-child(7)').val(),
                    dataType: 'json',
                    success: function(json) {
                        if (json['success']) {
                            $(this).text('Готово');
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        
                    }
                });
        }
        </script>
</html>
