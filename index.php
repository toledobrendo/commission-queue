<?php
    ini_set('display_errors', 1);
    require_once 'php/controller/IndexController.php'
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
                    <div class="col-6"><a class="float-right" href="no-javascript.html" id="add-button"><i class="fas fa-plus"></i> Add Queue</a></div>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col" style="width: 15%">Start Date</th>
                        <th scope="col" style="width: 15%">Progress</th>
                        <th scope="col" style="width: 10%">Paid</th>
                        <th scope="col" style="width: 15%">Actions</th>
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
                        <td>
                            <button class="btn btn-light btn-sm up-button" data-toggle="tooltip" data-placement="top" title="Move up">
                                <i class="fas fa-caret-up"></i></button>
                            <button class="btn btn-light btn-sm down-button" data-toggle="tooltip" data-placement="top" title="Move down">
                                <i class="fas fa-caret-down"></i></button>
                            <button class="btn btn-success btn-sm edit-button" data-toggle="tooltip" data-placement="top" title="Edit">
                                <i class="fas fa-edit"></i></button>
                            <button class="btn btn-danger btn-sm delete-button" data-toggle="tooltip" data-placement="top" title="Delete">
                                <i class="fas fa-times"></i></button>
                            <div class="d-none comm-id"><?=$comm->getId()?></div>
                            <div class="d-none priority"><?=$comm->getPriority()?></div>
                        </td>
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
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">New message</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label for="comm-name" class="col-form-label">Name:</label>
                            <input type="text" class="form-control" id="comm-name" name="commName" required>
                        </div>
                        <div class="form-group">
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
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Send message</button>
                </div>
            </div>
        </div>
    </div>

    <script type="application/javascript">
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

            $('#commModal').modal('toggle');

            e.preventDefault();
        });
    </script>
</body>
</html>