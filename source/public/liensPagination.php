<?php
/**
 * @var Pagination $pagination
 * @var string $baseUrlPagination
 * @var int $page
 */
?>

<div id="pagination">
    <?php
    if ($pagination->nbTotalPages() > 1) {


        if ($pagination->pagePrecedenteExiste()) {
            echo " <a href=\"{$baseUrlPagination}";
            echo $pagination->getNPagePrec();
            echo "\">&laquo; Précédente</a> ";
        }

        for ($i = 1; $i <= $pagination->nbTotalPages(); $i++) {
            if ($i == $page) {
                echo " <span class=\"selected\">{$i}</span> ";
            } else {
                echo " <a href=\"{$baseUrlPagination}{$i}\">{$i}</a> ";
            }
        }

        if ($pagination->pageSuivanteExiste()) {
            echo " <a href=\"{$baseUrlPagination}";
            echo $pagination->getNPageSuiv();
            echo "\">Suivante &raquo;</a> ";
        }

    }

    ?>
</div>