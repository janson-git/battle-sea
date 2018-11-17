<head>
    <style>
        .game-field {
            margin-left: auto;
            margin-right: auto;
            width: 300px;
            height: 300px;
        }
        .game-field-row {
            margin-top: -8px;
            padding: 0;
        }
        .game-field-cell {
            padding: 0;
            margin-left: -6px;
            display: inline-block;
            width: 30px;
            height: 30px;
            border: 1px solid #ccc;
            border-collapse: collapse;
        }
        .cell-empty {
            background-color: #fff;
        }
        .cell-ship {
            background-color: #339;
        }
        .cell-hit {
            background-color: #f00;
        }
        .cell-miss {
            background-color: #ccc;
        }
    </style>
</head>

<body>

    <div class="page-head">
        <button value="New Game">New Game</button>
    </div>


    @yield('content')

    <div id="app">
        <game-container></game-container>
    </div>
    <script src="/js/app.js"></script>
</body>