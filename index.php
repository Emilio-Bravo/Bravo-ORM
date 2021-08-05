<?php

use Bravo\ORM\Model;

require_once __DIR__ . '/vendor/autoload.php';

$users = Model::orderBy('id')->get();

if (!empty($_POST)) {
    Model::insert($_POST);
    header("location:index.php");
}

if (isset($_GET['id'])) {
    Model::delete(['id' => $_GET['id']]);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
    <title>Document</title>
</head>

<body>
    <div class="container py-4">

        <div id="alert-container"></div>

        <form action="index.php" method="post" class="py-5">
            <div class="row justify-content-center align-items-center py-3">
                <div class="col">
                    <input type="text" name="name" id="name" class="form-control" placeholder="Name" autocomplete="off">
                </div>
                <div class="col">
                    <input type="text" name="surname" id="surname" class="form-control" placeholder="Surname" autocomplete="off">
                </div>
                <div class="col">
                    <input type="email" name="email" id="email" class="form-control" placeholder="Email" autocomplete="off">
                </div>
                <div class="col">
                    <input type="text" name="password" id="password" class="form-control" placeholder="Password" autocomplete="off">
                </div>
                <div class="col">
                    <button class="btn btn-success form-control">Register</button>
                </div>
            </div>
        </form>


        <table class="table table-striped text-center table-bordered table-hover">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Surname</th>
                    <th scope="col">Email</th>
                    <th scope="col">Password</th>
                    <th scope="col">Options</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user) : ?>
                    <tr>
                        <td><?= $user->id ?></td>
                        <td><?= $user->name ?></td>
                        <td><?= $user->surname ?></td>
                        <td><?= $user->email ?></td>
                        <td><?= $user->password ?></td>
                        <td><a href="#" class="btn btn-danger delete-btn" ui="<?= $user->id ?>">Delete</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>

    <script>
        window.addEventListener('load', () => {

            const deleteBtn = document.querySelectorAll('.table .delete-btn');

            const sendRequest = async (url) => {
                try {
                    return await fetch(url);
                } catch (err) {
                    console.log(err);
                    return false;
                }
            }

            const insertAlert = (msg, type = 'success') => {
                let template = `
                            <div class='alert alert-${type} alert-dismissible fade show md-3' role='alert'>
                                ${msg}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `;
                document.querySelector('#alert-container').innerHTML = template;
            }

            deleteBtn.forEach(async (btn) => {
                btn.addEventListener('click', () => {
                    if (sendRequest(`index.php?id=${btn.getAttribute('ui')}`)) {
                        btn.parentNode.parentNode.style.display = 'none';
                        insertAlert(`User ${btn.getAttribute('ui')} deleted successfuly`);
                    } else {
                        insertAlert('Something went wrong', 'danger');
                    }
                });
            });

        })
    </script>

</body>

</html>