<?php
try {
    echo "DB OK";
} catch (Exception $e) {
    echo "DB ERRO: " . $e->getMessage();
}
