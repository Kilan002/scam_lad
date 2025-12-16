from flask import Flask, request, jsonify
from flask_cors import CORS
import os
import glob
import google.generativeai as genai
from bs4 import BeautifulSoup

app = Flask(__name__)
CORS(app)  # Permite que tu página web hable con este script

# --- CONFIGURACIÓN GEMINI ---
API_KEY = "AIzaSyBy-fYRN_eMD3U1uxr6et63dTgm0cS_Bmw" # <--- ¡PON TU API KEY!
REPO_PATH = "./scam-lad"  # Asegúrate que la ruta sea correcta
genai.configure(api_key=API_KEY)

# --- LEER EL REPO ---
def leer_contenido_repo(ruta_base):
    contenido_total = ""
    # Busca en la ruta actual y subcarpetas
    archivos = glob.glob(os.path.join(ruta_base, "**", "*.html"), recursive=True) + \
               glob.glob(os.path.join(ruta_base, "**", "*.php"), recursive=True)
    
    print(f"Cargando {len(archivos)} archivos...")
    for archivo in archivos:
        try:
            with open(archivo, 'r', encoding='utf-8', errors='ignore') as f:
                soup = BeautifulSoup(f.read(), 'html.parser')
                contenido_total += f"\n--- {os.path.basename(archivo)} ---\n{soup.get_text(separator=' ', strip=True)}"
        except: pass
    return contenido_total

contexto = leer_contenido_repo(REPO_PATH)
model = genai.GenerativeModel('gemini-2.5-flash')

# Iniciamos el chat una sola vez con el contexto
chat = model.start_chat(history=[
    {"role": "user", "parts": [f"Eres un experto en el sitio web Scam Lad. Basa tus respuestas en esto: {contexto}"]}
])

# --- RUTA DE LA API ---
@app.route('/preguntar', methods=['POST'])
def preguntar():
    datos = request.json
    mensaje_usuario = datos.get('mensaje')
    
    if not mensaje_usuario:
        return jsonify({"respuesta": "Por favor escribe algo."})

    try:
        response = chat.send_message(mensaje_usuario)
        return jsonify({"respuesta": response.text})
    except Exception as e:
        return jsonify({"respuesta": f"Error: {str(e)}"})

# Ejecutar servidor en el puerto 5000
if __name__ == '__main__':
    # Cambia '0.0.0.0' por '127.0.0.1' para mayor seguridad
    app.run(host='127.0.0.1', port=5000)
