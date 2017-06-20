<?= message_box('success'); ?>
<?= message_box('error'); ?>
<div class="box box-success">
    <div class="panel-heading">
        <div class="panel-title">
            <?= lang('all_project') ?>
        </div>
    </div>
    <div class="box-body">
        <div class="table-responsive">
            <table class="table table-striped DataTables " id="DataTables">
                <thead>
                    <tr>                            
                        <th><?= lang('project_name') ?></th>                                                                                                                         
                        <th><?= lang('start_date') ?></th>                            
                        <th><?= lang('end_date') ?></th>                            
                        <th><?= lang('status') ?></th>                            
                        <th class="col-options no-sort" ><?= lang('action') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $client_id = $this->session->userdata('client_id');
                    $all_project = $this->db->where('client_id', $client_id)->get('tbl_project')->result();                    
                    if (!empty($all_project)):foreach ($all_project as $v_project):
                            ?>
                            <tr>                                                                    
                                <td><a class="text-info" href="<?= base_url() ?>client/project/project_details/<?= $v_project->project_id ?>"><?= $v_project->project_name ?></a>
                                    <?php if (time() > strtotime($v_project->end_date) AND $v_project->progress < 100) { ?>
                                        <span class="label label-danger pull-right"><?= lang('overdue') ?></span>
                                    <?php } ?>

                                    <div class="progress progress-xs progress-striped active">
                                        <div class="progress-bar progress-bar-<?php echo ($v_project->progress >= 100 ) ? 'success' : 'primary'; ?>" data-toggle="tooltip" data-original-title="<?= $v_project->progress ?>%" style="width: <?= $v_project->progress; ?>%"></div>
                                    </div>

                                </td>                                
                                <td><?= strftime(config_item('date_format'), strtotime($v_project->start_date)) ?></td>
                                <td><?= strftime(config_item('date_format'), strtotime($v_project->end_date)) ?></td>

                                <td><?php
                                    if (!empty($v_project->project_status)) {
                                        if ($v_project->project_status == 'completed') {
                                            $status = "<span class='label label-success'>" . lang($v_project->project_status) . "</span>";
                                        } elseif ($v_project->project_status == 'in_progress') {
                                            $status = "<span class='label label-primary'>" . lang($v_project->project_status) . "</span>";
                                        } elseif ($v_project->project_status == 'cancel') {
                                            $status = "<span class='label label-danger'>" . lang($v_project->project_status) . "</span>";
                                        } else {
                                            $status = "<span class='label label-warning'>" . lang($v_project->project_status) . "</span>";
                                        }
                                        echo $status;
                                    }
                                    ?>      </td>     
                                <td>
                                    <?= btn_view('client/project/project_details/' . $v_project->project_id) ?>                                                                        
                                </td>
                            </tr>
                            <?php
                        endforeach;
                    endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
