<?php
global $APPLICATION;
if ($errorException = $APPLICATION->getException()) {
    CAdminMessage::showMessage(
        'Error:' . $errorException->GetString()
    );
} else {
    CAdminMessage::showNote(
        'Successfully installed.'
    );
}