<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <?php echo '<a href="' . $url_home . '">' . $home; ?></a>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <?php echo '<a href="' . $url_parent1 . '">' . $parent1; ?></a>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <?php echo '<a href="' . $url_parent2 . '">' . $parent2; ?></a>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <span><?php echo $title; ?></span>
                </li>
            </ul>
            <div class="page-toolbar">
                <div id="dashboard-report-range" class="pull-right tooltips btn btn-sm" data-container="body" data-placement="bottom">
                    <span class="thin uppercase hidden-xs" id="datetime"></span>
                    <script type="text/javascript">window.onload = date_time('datetime');</script>
                </div>
            </div>
        </div>
        <br />
        <div class="portlet box blue-madison">
            <div class="portlet-title">
                <div class="caption"><h4><?php echo $title; ?></h4></div>
                <div class="tools">
                    <div class="pull-right">
                        <a href="<?php echo site_url('configuration/privilege'); ?>"
                           class="btn btn-warning">Kembali</a>
                    </div>
                </div>
            </div>
            <div class="portlet-body">
                <form action="<?php echo site_url('configuration/privilege/update'); ?>" method="post"
                      id="form-input" class="form-horizontal">
                    <input type="hidden" name="id" value="<?php echo $privilege->id; ?>"/>
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3">Nama Grup<span
                                    class="required"> * </span>
                            </label>
                            <div class="col-md-6">
                                <select name="group_name" class="form-control">
                                    <option disabled>Pilih</option>
                                    <?php
                                    $id = $privilege->group_id;
                                    foreach ($groups as $group) {
                                        echo $group->id;
                                        $selected = $id == $group->id ? 'selected' : '';
                                        echo '<option ' . $selected . ' value="' . $group->id . '">' . $group->name . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Nama Hak Akses<span
                                    class="required"> * </span>
                            </label>
                            <div class="col-md-6">
                                <input type="text" name="privilege_name" data-required="1" class="form-control" value="<?php echo $privilege->privilege_name; ?>"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Deskripsi</label>
                            <div class="col-md-6">
                                <textarea name="privilege_desc" class="form-control" id="privilege-id" rows="5"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="40%">Nama Menu</th>
                                    <th class="text-center" width="10%">View</th>
                                    <th class="text-center" width="10%">Add</th>
                                    <th class="text-center" width="10%">Edit</th>
                                    <th class="text-center" width="10%">Delete</th>
                                    <th class="text-center" width="10%">Detail</th>
                                    <th class="text-center" width="10%">Approval</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                /* Fungsi cek id menu yang sama */

                                function is_selected_menu($menu_id, $pd1) {

                                    if ($pd1 == $menu_id) {
                                        $selected = 'checked';
                                    } else {
                                        $selected = '';
                                    }

                                    return $selected;
                                }
                                ?>
                                <?php foreach ($menus as $menu): ?>
                                    <tr>
                                        <td>
                                            <label class="mt-checkbox mt-checkbox-outline">
                                                <input type="checkbox" name="menu[]" value="<?php echo $menu->id; ?>"
                                                <?php
                                                foreach ($privilege_detail as $pd1) {
                                                    echo is_selected_menu($menu->id, $pd1->menu_id);
                                                }
                                                ?>
                                                       />
                                                <span></span>
                                            </label>
                                            <?php echo $menu->name; ?>
                                        </td>
                                        <?php
                                        $view = $this->privilege_model->getAccessButton('view', $privilege->id, $menu->id);
                                        $add = $this->privilege_model->getAccessButton('add', $privilege->id, $menu->id);
                                        $edit = $this->privilege_model->getAccessButton('edit', $privilege->id, $menu->id);
                                        $delete = $this->privilege_model->getAccessButton('delete', $privilege->id, $menu->id);
                                        $detail = $this->privilege_model->getAccessButton('detail', $privilege->id, $menu->id);
                                        $approval = $this->privilege_model->getAccessButton('approval', $privilege->id, $menu->id);
                                        ?>
                                        <td class="text-center">
                                            <label class="mt-checkbox mt-checkbox-outline">
                                                <input type="checkbox" name="view[]" value="<?php echo $menu->id; ?>" <?php echo $view == 't' ? 'checked' : ''; ?> />
                                                <span></span>
                                            </label>
                                        </td>
                                        <td class="text-center">
                                            <label class="mt-checkbox mt-checkbox-outline">
                                                <input type="checkbox" name="add[]" value="<?php echo $menu->id; ?>" <?php echo $add == 't' ? 'checked' : ''; ?>/>
                                                <span></span>
                                            </label>
                                        </td>
                                        <td class="text-center">
                                            <label class="mt-checkbox mt-checkbox-outline">
                                                <input type="checkbox" name="edit[]" value="<?php echo $menu->id; ?>" <?php echo $edit == 't' ? 'checked' : ''; ?>/>
                                                <span></span>
                                            </label>
                                        </td>
                                        <td class="text-center">
                                            <label class="mt-checkbox mt-checkbox-outline">
                                                <input type="checkbox" name="delete[]" value="<?php echo $menu->id; ?>" <?php echo $delete == 't' ? 'checked' : ''; ?>/>
                                                <span></span>
                                            </label>
                                        </td>
                                        <td class="text-center">
                                            <label class="mt-checkbox mt-checkbox-outline">
                                                <input type="checkbox" name="detail[]" value="<?php echo $menu->id; ?>" <?php echo $detail == 't' ? 'checked' : ''; ?>/>
                                                <span></span>
                                            </label>
                                        </td>
                                        <td class="text-center">
                                            <label class="mt-checkbox mt-checkbox-outline">
                                                <input type="checkbox" name="approval[]" value="<?php echo $menu->id; ?>" <?php echo $approval == 't' ? 'checked' : ''; ?>/>
                                                <span></span>
                                            </label>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-6">
                                <button type="submit" class="btn btn-warning">Update</button>
                                <button type="reset" class="btn default">Batal</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var FormValidation = function () {

        var handleValidation = function () {

            var form = $('#form-input');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);

            form.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "", // validate all fields including form hidden input
                rules: {
                    group_name: {
                        required: true
                    },
                    privilege_name: {
                        required: true
                    }
                },

                errorPlacement: function (error, element) { // render error placement for each input type
                    if (element.parent(".input-group").size() > 0) {
                        error.insertAfter(element.parent(".input-group"));
                    } else if (element.attr("data-error-container")) {
                        error.appendTo(element.attr("data-error-container"));
                    } else if (element.parents('.radio-list').size() > 0) {
                        error.appendTo(element.parents('.radio-list').attr("data-error-container"));
                    } else if (element.parents('.radio-inline').size() > 0) {
                        error.appendTo(element.parents('.radio-inline').attr("data-error-container"));
                    } else if (element.parents('.checkbox-list').size() > 0) {
                        error.appendTo(element.parents('.checkbox-list').attr("data-error-container"));
                    } else if (element.parents('.checkbox-inline').size() > 0) {
                        error.appendTo(element.parents('.checkbox-inline').attr("data-error-container"));
                    } else {
                        error.insertAfter(element); // for other inputs, just perform default behavior
                    }
                },

                invalidHandler: function (event, validator) { //display error alert on form submit
                    success.hide();
                    error.show();
                    App.scrollTo(error, -200);
                },

                highlight: function (element) { // hightlight error inputs
                    $(element)
                            .closest('.form-group').addClass('has-error'); // set error class to the control group
                },

                unhighlight: function (element) { // revert the change done by hightlight
                    $(element)
                            .closest('.form-group').removeClass('has-error'); // set error class to the control group
                },

                success: function (label) {
                    label
                            .closest('.form-group').removeClass('has-error'); // set success class to the control group
                },

                // submitHandler: function (form) {
                //     success.show();
                //     error.hide();
                //     form[0].submit(); // submit the form
                // }

            });
        }

        return {
            //main function to initiate the module
            init: function () {

                handleValidation();

            }

        };

    }();

    jQuery(document).ready(function () {
        FormValidation.init();
    });
</script>
