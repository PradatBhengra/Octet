<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>NIC Jharkhand - Employee Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../../assets/vendor/bootstrap/css/bootstrap.min.css">
  
  
  <style>
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f0f4f8;
      overflow: hidden; /* Prevent scrolling */
    }

    .wrapper {
      display: flex;
      flex-direction: column;
      height: 100vh; /* Full screen layout */
    }

    .nic-header {
      background-color: #003366;
      color: white;
      border-bottom: 6px solid #ff9933;
      padding: 15px 0;
      box-shadow: 0px 3px 10px rgba(0, 0, 0, 0.2);
    }

    .nic-header .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
    }

    .nic-logo {
      height: 60px;
    }

    .jh-emblem {
      height: 60px;
    }

    .nic-header h1 {
      flex: 1;
      text-align: center;
      font-size: 22px;
      font-weight: 700;
      color: #ffcc00;
      margin: 0 20px;
    }

    .lang-access {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 14px;
    }

    .lang-access a {
      color: #ffcc00;
      text-decoration: none;
      font-weight: 600;
    }

    .lang-access a:hover {
      text-decoration: underline;
    }

    .lang-access button {
      background-color: #ff9933;
      color: #003366;
      border: none;
      padding: 4px 10px;
      font-weight: bold;
      font-size: 14px;
      border-radius: 3px;
      cursor: pointer;
    }

    .lang-access button:hover {
      background-color: #e67e22;
    }

    main {
      flex: 1;
      overflow-y: auto;
      padding: 20px;
    }

    @media (max-width: 768px) {
      .nic-header .container {
        flex-direction: column;
        align-items: center;
        text-align: center;
      }

      .nic-header h1 {
        font-size: 18px;
        margin-top: 10px;
      }
    }
  </style>
</head>
<body>
<div class="wrapper">
  <header class="nic-header">
    <div class="container">
      <img src="https://www.presentations.gov.in/wp-content/uploads/2020/06/nic-logo-nic-logo-1-bilingual-white-1-01.jpg" class="nic-logo" alt="NIC Logo">
      <h1>NIC Jharkhand - IP Management System</h1>
      <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQF_77pox1wMORrigecBHkLE9yrfQSWNo-JBA&s" class="jh-emblem" alt="Jharkhand Emblem">
      <div class="lang-access">
        <a href="#">हिन्दी</a> | <a href="#">English</a>
        <button id="increase-font">A⁺</button>
        <button id="decrease-font">A⁻</button>
      </div>
    </div>
  </header>

  <main class="container">
