<?php if(TR_DEBUG == true) : ?>
<div id="export-theme-options">
    <h2>Export</h2>
    <p><a class="button button-primary" href="<?php echo admin_url(); ?>themes.php?page=theme_options&theme-options=export">Export Theme Options as JSON</a></p>

    <h2>Import</h2>
    <form id="import-theme-options" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data">
        <p><input type="file" name="fileToUpload" id="fileToUpload"></p>
        <p><input type="submit" value="Import JSON" class="button button-primary" name="tr_theme_options_import"></p>
    </form>
</div>
<?php endif; ?>