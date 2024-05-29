        <div class="modal container-center<?= empty($_SESSION["modalMessage"]) ? "" : " show-modal" ?>" id="modal">
            <div class="container container-center modal-content">
<?php
echo "<div class='container-col'>$_SESSION[modalMessage]</div>";
$_SESSION["modalMessage"] = "";
?>
                <span class="close" onclick="dismissModal()">&times;</span>
            </div>
        </div>
