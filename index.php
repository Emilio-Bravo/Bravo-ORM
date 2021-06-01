<?php

use Bravo\ORM\Model;

require_once __DIR__ . '/vendor/autoload.php';

$users = Model::index(10);

if (!empty($_POST)) {
    Model::insert($_POST);
    header("location:index.php");
}

?>
<div class="container">
    <form action="index.php" method="post">
        <input type="text" name="id" placeholder="id" autocomplete="off" />
        <input type="text" name="name" placeholder="nombre" autocomplete="off" />
        <input type="text" name="email" placeholder="email" autocomplete="off" />
        <input type="text" name="password" placeholder="contraseña" autocomplete="off" />
        <button>Registrar</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Password</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user) : ?>
                <tr>
                    <td><?= $user->id ?></td>
                    <td><?= $user->name ?></td>
                    <td><?= $user->email ?></td>
                    <td><?= $user->password ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<style>
    * {
        font-family: sans-serif;
    }

    .container {
        width:80%;
        margin: auto;
        margin-top: 200px;
        box-shadow:0 0 5px #000;
        background: #bcbbbb;
    }

    .container table, .container form {
        margin: auto;        
        width: 80%;
    }

    .container form {
        background-color: #f48024;
        box-shadow: 0 0 3px #000;
        padding:5px 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .container form input {
        margin: 5px;
        outline: 0;
    }

    table {
        text-align: center;
        background: #222426;
        color:#fff;
    }

    table th,
    table td {
        padding: 2px;
        border: 1px solid #fff;
    }
</style>