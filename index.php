<?php
    ini_set('display_errors', 1);
    require_once 'php/controller/IndexController.php';
?>
<html>
<head>
    <title>My Tasks</title>
    <link rel="stylesheet" href="lib/bootstrap/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link href="lib/fontawesome/css/all.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <script src="lib/jquery/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="lib/bootstrap/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container">
        <div class="card border-secondary mt-3">
            <div class="card-header">
                <div class="row">
                    <div class="col">Sol's Commission Queue</div>
                    <?php if (isset($_SESSION['username'])) { ?>
                        <div class="col-6"><a class="float-right" href="no-javascript.html" id="add-button"><i class="fas fa-plus"></i> Add Commission</a></div>
                    <?php } else { ?>
                        <div class="col-6"><a class="float-right" href="no-javascript.html" id="login-button"><i class="fas fa-sign-in-alt"></i> Login</a></div>
                    <?php }?>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-success alert-dismissible fade show hide" role="alert" style="display:none;" id="success-alert">
                    <div id="alert-success-message">Successfully edited commission</div>
                    <button type="button" class="close alert-close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="alert alert-danger alert-dismissible fade show collapse" role="alert" style="display:none;" id="error-alert">
                    <div id="alert-error-message">Error</div>
                    <button type="button" class="close alert-close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col" style="width: 15%">Start Date</th>
                        <th scope="col" style="width: 15%">Progress</th>
                        <th scope="col" style="width: 10%">Paid</th>
                        <?php if (isset($_SESSION['username'])) { ?>
                            <th scope="col" style="width: 15%">Actions</th>
                        <?php } ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($comms as &$comm) { ?>
                    <tr id="task-0">
                        <td class="name"><?=$comm->getName()?></td>
                        <td class="start-date"><?=$comm->getStartDate()?></td>
                        <td class="comm-progress"><?=$comm->getProgress()?></td>
                        <?php if ($comm->getPaid()) {?>
                        <td><i class="fas fa-check text-success"></i><div class="d-none paid">true</div></td>
                        <?php } else {?>
                        <td><i class="fas fa-times text-danger"></i><div class="d-none paid">false</div></td>
                        <?php }?>
                        <?php if (isset($_SESSION['username'])) { ?>
                            <td>
                                <?php if ($comm->getPriority() != 1) {?>
                                    <button class="btn btn-primary btn-sm edit-button" data-toggle="tooltip" data-placement="top" title="Edit">
                                        <i class="fas fa-edit"></i></button>
                                    <a class="btn btn-danger btn-sm delete-button" href="delete-comm.php?id=<?=$comm->getId()?>" data-toggle="tooltip" data-placement="top" title="Delete">
                                        <i class="fas fa-times"></i></a>
                                    <?php if ($comm->getPriority() > 2) { ?>
                                        <a class="btn btn-light btn-sm up-button" href="change-prio.php?action=up&id=<?=$comm->getId()?>" data-toggle="tooltip" data-placement="top" title="Move up">
                                            <i class="fas fa-caret-up"></i></a>
                                    <?php } ?>
                                    <?php if ($comm->getPriority() != $leastPrio) { ?>
                                        <a class="btn btn-light btn-sm down-button" href="change-prio.php?action=down&id=<?=$comm->getId()?>" data-toggle="tooltip" data-placement="top" title="Move down">
                                            <i class="fas fa-caret-down"></i></a>
                                    <?php }?>
                                <?php } else { ?>
                                    <button class="btn btn-primary btn-sm edit-button" data-toggle="tooltip" data-placement="top" title="Edit">
                                        <i class="fas fa-edit"></i></button>
                                    <a class="btn btn-success btn-sm complete-button" href="delete-comm.php?id=<?=$comm->getId()?>" data-toggle="tooltip" data-placement="top" title="Complete">
                                        <i class="fas fa-thumbs-up"></i></a>
                                <?php }?>
                                <div class="d-none comm-id"><?=$comm->getId()?></div>
                                <div class="d-none priority"><?=$comm->getPriority()?></div>
                            </td>
                        <?php } ?>
                    </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="commModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="add-commission.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">New Commission</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="comm-name" class="col-form-label">Name:</label>
                            <input type="text" class="form-control" id="comm-name" name="commName" required autofocus>
                        </div>
                        <div class="form-group" id="progress-group">
                            <label for="message-text" class="col-form-label">Progress:</label>
                            <div class="w-100"></div>
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-secondary">
                                    <input type="radio" name="progress" value="Queued"> Queued
                                </label>
                                <label class="btn btn-secondary">
                                    <input type="radio" name="progress" value="Rough"> Rough
                                </label>
                                <label class="btn btn-secondary">
                                    <input type="radio" name="progress" value="Line"> Line
                                </label>
                                <label class="btn btn-secondary">
                                    <input type="radio" name="progress" value="Render"> Render
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">Paid:</label>
                            <div class="w-100"></div>
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-secondary">
                                    <input type="radio" name="paid" value="true"> Yes
                                </label>
                                <label class="btn btn-secondary">
                                    <input type="radio" name="paid" value="false"> No
                                </label>
                            </div>
                        </div>
                        <input type="hidden" id="comm-id" name="id"/>
                        <input type="hidden" id="priority" name="priority"/>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="loginModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="login.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">Login</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="comm-name" class="col-form-label">Username:</label>
                            <input type="text" class="form-control" id="username" name="commName" value="sol" disabled>
                        </div>
                        <div class="form-group">
                            <label for="comm-name" class="col-form-label">Password:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="application/javascript">
        let params = new URLSearchParams(window.location.search);
        if (params.has('success')) {
            let param = params.get('success');
            if (param === 'edit') {
                $('#alert-success-message').text('Successfully edited commission');
            } else if (param === 'create') {
                $('#alert-success-message').text('Successfully created new commission');
            } else if (param === 'delete') {
                $('#alert-success-message').text('Successfully deleted/completed commission');
            } else if (param === 'up') {
                $('#alert-success-message').text('Successfully prioritized ' + params.get('target'));
            } else if (param === 'down') {
                $('#alert-success-message').text('Successfully de-prioritized ' + params.get('target'));
            }
            $('#success-alert').show();
            setTimeout(function() {
                $('#success-alert').hide();
            }, 5000);
        }
        if (params.has('error')) {
            let param = params.get('error');
            if (param === 'input') {
                $('#alert-error-message').text('Commission details are not complete');
            } else if (param === 'no-id') {
                $('#alert-error-message').text('Commission does not exist');
            } else if (param === 'invalid-action') {
                $('#alert-error-message').text('Invalid action');
            } else if (param === 'inc-cred') {
                $('#alert-error-message').text('Wrong credentials');
            }
            $('#error-alert').show();
            setTimeout(function() {
                $('#error-alert').hide();
            }, 5000);
        }

        $('#login-button').click(function(e) {
            $('#password').val(null);

            $('#loginModal').modal('toggle');

            e.preventDefault();
        });

        $('.edit-button').click(function() {
            $('#modalLabel').text('Edit Commission');

            console.log("entered modal");
            let name = $(this).parentsUntil('tbody').find('.name').get(0).innerText;
            let progress = $(this).parentsUntil('tbody').find('.comm-progress').get(0).innerText;
            let paid = $(this).parentsUntil('tbody').find('.paid').get(0).innerText;
            let id = $(this).parentsUntil('tbody').find('.comm-id').get(0).innerText;
            let priority = $(this).parentsUntil('tbody').find('.priority').get(0).innerText;

            $('#comm-name').val(name);
            $('#comm-id').val(id);
            $('#priority').val(priority);
            $('input[name="progress"]').prop('checked', false).parent().removeClass('active');
            $('input[name="progress"][value="' + progress + '"]').prop('checked', true).parent().addClass('active');
            $('input[name="paid"]').prop('checked', false).parent().removeClass('active');
            $('input[name="paid"][value="' + paid + '"]').prop('checked', true).parent().addClass('active');

            if (priority != 1) {
                $('#progress-group').addClass('d-none');
            }

            $('#commModal').modal('toggle');
        });

        $('#add-button').click(function(e) {
            $('#modalLabel').text('New Commission');

            $('#comm-name').val(null);
            $('#comm-id').val(null);
            $('#priority').val(null);
            $('input[name="progress"]').prop('checked', false).parent().removeClass('active');
            $('input[name="progress"][value="Queued"]').prop('checked', true).parent().addClass('active');
            $('input[name="paid"]').prop('checked', false).parent().removeClass('active');
            $('input[name="paid"][value="false"]').prop('checked', true).parent().addClass('active');

            $('#progress-group').addClass('d-none');

            $('#commModal').modal('toggle');

            e.preventDefault();
        });
    </script>
</body>
</html>