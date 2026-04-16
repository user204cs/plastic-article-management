<?php
// Gestion du CORS et pré-flight
if (
    isset($_SERVER['REQUEST_METHOD']) && 
    $_SERVER['REQUEST_METHOD'] === 'OPTIONS'
) {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    exit;
}
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Hardcoded list of couleurs
$colors = [
    'BEIGE',
    'Corail',
    'MARRON',
    'NOIR',
    'VERT',
    'VIOLET',
    'BLANC',
    'BLANC FUME',
    'BLEU FUME',
    'GRIS ANTHRACITE',
    'MAUVE',
    'BLEU CIEL',
    'BLANC 1054',
    'BLEU TURQUOISE',
    'GRIS',
    'ROUGE',
    'BLEU',
    'JAUNE',
    'ROSE',
    'ORANGE',
    'ROSE PASTEL',
    'IML ANONYMOUS',
    'IML BARBY',
    'IML MAROC',
    'IML SPIDERMAN',
    'BEIGE DECO',
    'DECO TAUPE BOIS',
    'MARBRE BLANC',
    'NARUTO',
    'TAUPE BOIS',
    'FUSHIA',
    'VERT PASTEL',
    'TRANSPARENT',
    'JUTE',
    'KHMISSA',
    'AZULEJOS',
    'ROSACE',
    'CITRON',
    'BOIS ROUGE',
    'GRIS DECO',
    'IML HARICO',
    'IML KHMISSA',
    'IML MACCARON',
    'DECO SPIDRMAN',
    'IML HARIBO',
    'MACCARONS BLEU',
    'PASTEC',
    'REINE DES NEIGES',
    'IML ANANAS',
    'IML FRAMBOISE',
    'IML PASTEQUE',
    'IML SALADE TOMATE',
    'BRODERIE',
    'CŒUR',
    'IML FLORY',
    'PAPILLON',
    'IML FLEUR BLANC',
    'TAOUESS',
    'DECO-FLAMANDS'
];
$rows = [];
foreach ($colors as $index => $c) {
    $rows[] = ['id' => $index + 1, 'couleur' => $c];
}
echo json_encode(['success' => true, 'data' => $rows]);
exit;
?>
