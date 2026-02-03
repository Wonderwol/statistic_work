    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background-color: #6d444b;
            color: white;
            margin-bottom: 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        /* Навигационная панель */
        .navigation-panel {
            background-color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin: 0 auto 30px;
            max-width: 1200px;
            border-radius: 0 0 8px 8px;
            padding: 15px 20px;
        }
        
        .nav-content {
            display: flex;
            gap: 30px;
        }
        
        .nav-section {
            flex: 1;
        }
        
        .nav-section-title {
            font-weight: 600;
            color: #6d444b;
            margin-bottom: 10px;
            font-size: 14px;
            padding-bottom: 5px;
            border-bottom: 2px solid #98fb98;
        }
        
        .nav-menu {
            list-style: none;
        }
        
        .nav-menu li {
            margin-bottom: 5px;
        }
        
        .nav-menu a {
            display: block;
            padding: 8px 10px;
            text-decoration: none;
            color: #333;
            border-radius: 4px;
            transition: all 0.3s;
            border-left: 2px solid transparent;
            font-size: 14px;
        }
        
        .nav-menu a:hover {
            background-color: #98fb98;
            color: black;
            border-left-color: #6d444b;
        }
        
        .nav-menu a.active {
            background-color: #6d444b;
            color: white;
            border-left-color: #98fb98;
        }
        
        h1 {
            font-size: 24px;
            font-weight: 300;
        }
        
        .filters {
            background-color: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .filters h2 {
            color: #6d444b;
            margin-bottom: 20px;
            font-size: 18px;
        }
        
        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
        }
        
        label {
            margin-bottom: 8px;
            font-weight: 600;
            color: #6d444b;
        }
        
        select, input[type="text"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        select:focus, input[type="text"]:focus {
            outline: none;
            border-color: #6d444b;
        }
        
        .buttons {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: background-color 0.3s;
        }
        
        .btn-primary {
            background-color: #6d444b;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #98fb98;
            color: black;
        }
        
        .btn-secondary {
            background-color: #a9a9a9;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #98fb98;
            color: black;
        }
        
        .results {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background-color: #6d444b;
            color: white;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            font-weight: 600;
        }
        
        tbody tr:hover {
            background-color: #98fb98;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-city {
            background-color: #98fb98;
            color: #1565c0;
        }
        
        .badge-village {
            background-color: #98fb98;
            color: #7b1fa2;
        }
        
        .statistics {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        
        .stat-card {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .stat-card h3 {
            color: #6d444b;
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        .stat-value {
            font-size: 32px;
            font-weight: bold;
            color: #6d444b;
        }
        
        .no-results {
            text-align: center;
            padding: 40px;
            color: #6d444b;
        }
        
        footer {
            margin-top: 50px;
            text-align: center;
            color: #6d444b;
            font-size: 14px;
            padding: 20px 0;
            border-top: 1px solid #eee;
        }
        
        .checkbox-group {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            margin-top: 5px;
            background-color: #f9f9f9;
        }
        
        .checkbox-item {
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }
        
        .checkbox-item input[type="checkbox"] {
            margin-right: 10px;
        }
        
        .checkbox-item label {
            margin-bottom: 0;
            font-weight: normal;
            cursor: pointer;
        }
        
        .filter-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }
        
        .select-all {
            font-size: 12px;
            color: #6d444b;
            cursor: pointer;
            text-decoration: underline;
        }
        
        .selected-count {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        
        .info {
            margin-left: 20px;
        }
        
        @media (max-width: 768px) {
            .nav-content {
                flex-direction: column;
                gap: 20px;
            }
            
            .filter-row {
                grid-template-columns: 1fr;
            }
            
            .header-content {
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }
            
            table {
                display: block;
                overflow-x: auto;
            }
            
            .navigation-panel {
                padding: 15px;
                margin: 0 10px 30px;
            }
        }
    </style>