<?php
use HDNET\Autoloader\Utility\TranslateUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

// @todo remove hdnet dependencies

// set configuration
$return = array(
    'state'          => 'OK',
    'description'    => TranslateUtility::assureLabel('eid.ok', 'faq', 'Vielen Dank f&uuml;r Ihre Wertung.'),
    'currentCounter' => 0
);

$table = 'tx_faq_domain_model_question';

$modes = array(
    'top',
    'flop'
);

$question = (int)GeneralUtility::_GET('question');
$mode = GeneralUtility::_GET('mode');

// parse configuration
if (!in_array($mode, $modes)) {
    $return['state'] = 'ERROR';
    $return['description'] = TranslateUtility::assureLabel('eid.error.mode', 'faq',
        'Es wurde kein Modus gesetzt. Bitte kontaktieren Sie den Administrator!');
} else {

    $var = session_id();
    if (empty($var) && !headers_sent()) {
        session_start();
    }

    if (!isset($_SESSION['topflop'])) {
        $_SESSION['topflop'] = serialize(array());
    }
    $ids = unserialize($_SESSION['topflop']);
    if (in_array($question, $ids)) {
        $return['state'] = 'ERROR';
        $return['description'] = TranslateUtility::assureLabel('eid.error.multivote', 'faq',
            'Sie haben f&uuml;r diese Frage bereits abgestimmt!');
    } else {
        $database = $GLOBALS['TYPO3_DB'];
        $row = $database->exec_SELECTgetSingleRow('uid,flop_counter,top_counter', $table, 'uid=' . $question . ' AND deleted=0');
        $query = $database->SELECTquery('uid,flop_counter,top_counter', $table, 'uid=' . $question . ' AND deleted=0');
        if (!is_array($row)) {
            $return['state'] = 'ERROR';
            $return['description'] = TranslateUtility::assureLabel('eid.error.question', 'faq',
                'Keine g&uuml;ltige Frage ausgew&auml;hlt!');
        } else {
            $ids[] = $row['uid'];
            $_SESSION['topflop'] = serialize($ids);
            $counter = ((int)$row[$mode . '_counter']) + 1;

            $update = array(
                $mode . '_counter' => $counter
            );
            $return['currentCounter'] = $counter;

            $database->exec_UPDATEquery($table, 'uid=' . $row['uid'], $update);
        }
    }
}

echo json_encode($return);