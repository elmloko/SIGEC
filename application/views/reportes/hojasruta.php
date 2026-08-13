<script>
    $(function () {
        //$('#demo-date').datepicker({autoclose: true, todayHighlight: true});
        //$('#demo-date-month').datepicker({autoclose: true, todayHighlight: true, minViewMode: 1});
        //$('#demo-date-format').datepicker({autoclose: true, todayHighlight: true, format: "yyyy/mm/dd"});
        $('#demo-date-range').datepicker({todayHighlight: true,format: "dd-mm-yyyy"});
        //$('#demo-date-inline').datepicker({todayHighlight: true});

    });

</script>
<div class="row">
    <div class="col-md-6">
        <form class="form" action="" method="POST">
            <div class="card card-underline">
                <div class="card-head">
                    <header>Correspodencia Derivada</header>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="input-daterange input-group" id="demo-date-range">
                            <div class="input-group-content">
                                <input type="text" class="form-control" name="start" value="<?php echo date('d-m-Y')?>" />
                                <label>Date range</label>
                            </div>
                            <span class="input-group-addon">to</span>
                            <div class="input-group-content">
                                <input type="text" class="form-control" name="end" value="<?php echo date('d-m-Y')?>" />
                                <div class="form-control-line"></div>
                            </div>
                        </div>
                    </div> 
                </div>

            </div>
        </form>
    </div>    
</div>
