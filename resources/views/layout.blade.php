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
            width: 29px; /** 30->29 fix to firefox borders render */
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

        .popup {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.5);
            text-align: center;
        }
        .popup-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            line-height: 1;
            cursor: pointer;
        }
    </style>
    <script>
      /**
       * Simple selector by ID or className (to not gets jquery for this)
       * Gets page elements by '#id' or '.className'
       * @param idOrClass
       * @returns {*}
       */
      var $ = function(idOrClass) {
        var type = String(idOrClass).substr(0, 1);
        var value = String(idOrClass).substr(1);
        var elem;
        switch (type) {
          case '#':
            elem = window.document.getElementById(value);
            break;
          case '.':
            elem = window.document.getElementsByClassName(value);
            break;
        }

        return elem;
      };
    </script>
</head>

<body>

    <div class="page-head">
        <button class="restart-game">Restart game</button>
    </div>


    @yield('content')

    <div class="popup modal" id="popup-game-over">
        <div class="popup-content">
            <span class="close">&times;</span>
            All ships is sunk!
            <button class="restart-game">Click to play again</button>
        </div>
    </div>

<script>
  //////////////////////////////////////////////////
  //////////////// FUNCTIONS ///////////////////////

  function openGameOverPopup() {
    $('#popup-game-over').style.display = 'block';
  }
  function closeGameOverPopup() {
    $('#popup-game-over').style.display = 'none';
  }

  function sendHit(rowNum, colNum, cb) {
    const xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
      if (parseInt(xhr.readyState) !== 4) {
        return;
      }

      cb(xhr.responseText);
    };

    xhr.open('GET', '/hit/' + rowNum + '/' + colNum, true);
    xhr.send();
  }

  //////////////////////////////////////////////////////
  //////////////// EVENT LISTENERS /////////////////////

  // close button clicked
  addEventListener('click', function(e) {
    if (e.target.className.indexOf('close') !== -1) {
      closeGameOverPopup();
    }
  });

  // restart game button handler
  addEventListener('click', function(e) {
    if (e.target.className.indexOf('restart-game') !== -1) {
      window.location.href = '/';
    }
  });

  // clicked on empty cell
  addEventListener('click', function(e) {
    var node = e.target;
    if (node.className.indexOf('cell-empty') !== -1) {
      var rowNum = node.attributes['data-row'].value;
      var colNum = node.attributes['data-col'].value;

      sendHit(rowNum, colNum, function (responseText) {
        var json = JSON.parse(responseText);
        var isHit = json.hit;
        var left = json.cellsLeft;

        var cell = $('#cell-' + rowNum + '-' + colNum);
        cell.className = cell.className.replace('cell-empty', isHit ? 'cell-hit' : 'cell-miss');

        if (left < 1) {
          openGameOverPopup();
        }
      });
    }
  });
</script>

    <script type="text/javascript" src="/js/autobahn.js"></script>
    <script>
        // TODO: При коннекте добавляем user id в виде /{user_id} - это будет наш ID пользователя/сесии
      var conn = new ab.Session('ws://localhost:8081/333222111',
        function() {
          conn.subscribe('test', function(topic, data) {
            // This is where you would add the new article to the DOM (beyond the scope of this tutorial)
            console.log('New article published to category "' + topic + '" : ' + data.data);
            console.log(data);
          });
        },
        function() {
          console.warn('WebSocket connection closed');
        },
        {'skipSubprotocolCheck': true}
      );
    </script>
</body>