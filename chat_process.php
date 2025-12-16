<?php
// Configuración
header('Content-Type: application/json');

// Clave de API de Gemini (SECRETA - SOLO EN EL SERVIDOR)
const GEMINI_API_KEY = 'AIzaSyC5hFodpl3ND7r9e-MWpAD_grKI_vf1XA4'; // REEMPLAZA ESTA LÍNEA CON TU CLAVE
const GEMINI_MODEL = 'gemini-2.5-flash';
const GEMINI_URL = 'https://generativelanguage.googleapis.com/v1beta/models/' . GEMINI_MODEL . ':generateContent';

// 1. Recibir la pregunta del usuario desde el frontend
$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['prompt'])) {
    http_response_code(400);
    echo json_encode(['error' => 'No se proporcionó un prompt.']);
    exit;
}
$user_prompt = $input['prompt'];

// 2. Definir el contexto del asistente (System Instruction)
// Esto guía a la IA para que actúe como Asistente de Scam LAD.
$system_instruction = "Eres el Asistente Virtual 'ScamBot' para la plataforma de videovigilancia 'Scam LAD'. Tu función es asistir a los usuarios, responder preguntas sobre el proyecto (registro, login, objetivos, funciones, acerca de) y proporcionar asistencia general. Utiliza la información clave: Scam LAD es un sistema para la supervisión y administración de cámaras de seguridad inteligente. Su objetivo es optimizar la vigilancia. Mantén un tono profesional y servicial. **NO** inventes funcionalidades de seguridad que no existan (ej: 'reconocimiento facial avanzado').";

// 3. Construir el cuerpo de la solicitud (Payload)
$data = [
    'contents' => [
        [
            'role' => 'user',
            'parts' => [
                ['text' => $user_prompt]
            ]
        ]
    ],
    'config' => [
        'systemInstruction' => $system_instruction,
        'temperature' => 0.7 // Un buen equilibrio entre creatividad y coherencia
    ]
];

// 4. Configurar y ejecutar la solicitud cURL
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, GEMINI_URL . '?key=' . GEMINI_API_KEY);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response = curl_exec($curl);
$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$curl_error = curl_error($curl);
curl_close($curl);

// 5. Procesar la respuesta
if ($curl_error || $http_code !== 200) {
    // Error de cURL o HTTP
    http_response_code(500);
    echo json_encode([
        'error' => 'Error al contactar la API de Gemini.',
        'details' => $curl_error ?: "Código HTTP: $http_code"
    ]);
    exit;
}

$api_data = json_decode($response, true);
$ai_response = 'Lo siento, no pude generar una respuesta.';

// Extraer el texto de la respuesta
if (isset($api_data['candidates'][0]['content']['parts'][0]['text'])) {
    $ai_response = $api_data['candidates'][0]['content']['parts'][0]['text'];
} elseif (isset($api_data['error'])) {
    // Si la API devuelve un error específico (ej: clave inválida)
    $ai_response = 'Error de la API: ' . $api_data['error']['message'];
}

// 6. Devolver la respuesta al frontend
echo json_encode(['response' => $ai_response]);

?>