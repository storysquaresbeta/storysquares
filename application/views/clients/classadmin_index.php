<style>.DTTT_container{display:none;}</style>
<div id="page-content" class="p20 clearfix">
    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1><?php echo lang('clients'); ?></h1>
           
        </div>
        <div class="table-responsive">
            <table id="client-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var showInvoiceInfo = true;
        if (!"<?php echo $show_invoice_info; ?>") {
            showInvoiceInfo = false;
        }

        $("#client-table").appTable({
            source: '<?php echo_uri("clients/classroom_list_data") ?>',
            columns: [
               
                {title: "<?php echo lang("company_name") ?>"},
                {title: "<?php echo lang("projects") ?>"} 
            ]
        });
    });
</script>