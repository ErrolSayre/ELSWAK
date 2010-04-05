<?php
require_once('ELSWAK/MySQL/Conditional.php');
require_once('ELSWAK/MySQL/Table/Join.php');
$tableJoin = new ELSWAK_MySQL_Table_Join('INNER', 'natural left outer', new ELSWAK_MySQL_Table('proposal_investigators', new ELSWAK_MySQL_Database('GrahamCracker')));
print_r_html($tableJoin);
$tableJoin->conditions()->addCondition(new ELSWAK_MySQL_Conditional(new ELSWAK_MySQL_Literal('proposals.PROPOSAL_ID'), new ELSWAK_MySQL_Operator('='), new ELSWAK_MySQL_Literal('proposal_investigators.PROPOSAL_ID')));
$tableJoin->conditions()->addCondition(new ELSWAK_MySQL_Conditional(new ELSWAK_MySQL_Literal('proposals.date_submitted'), new ELSWAK_MySQL_Operator('>='), new ELSWAK_MySQL_Literal('2000-01-01')));
print_r_html($tableJoin->sql());
?>