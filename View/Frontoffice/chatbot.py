import google.generativeai as genai
from flask import Flask
from flask_socketio import SocketIO, send

# Configure API key for GenAI
api_key = "AIzaSyBQ_klMDG_QAoph1FPkZ6a6CN3dyxtV7HM"  # Replace with your actual API key
genai.configure(api_key=api_key)

# Create the model configuration
generation_config = {
    "temperature": 0.5,
    "top_p": 0.8,
    "top_k": 40,
    "max_output_tokens": 1024,
    "response_mime_type": "text/plain",
}

# Initialize the generative model
model = genai.GenerativeModel(
    model_name="gemini-1.5-flash",
    generation_config=generation_config,
    system_instruction="""
You are a knowledgeable and professional assistant working for an educational and career development platform. Your primary role is to support users by providing detailed guidance on maximizing the value of the platform's features, which include simplified learning, networking opportunities, club participation, and CV building tools.

Use your expertise to help users understand how to:
- Join and engage with relevant learning modules and communities.
- Build a strong, personalized CV using platform tools.
- Connect effectively with peers and professionals through networking features.
- Participate in clubs and events that align with their interests and career goals.
- Earn and redeem rewards such as certificates, in-platform cosmetics, and cash prizes for performance and event participation.

When a user has questions or needs assistance, respond with clear, practical, and motivational advice that helps them take full advantage of the platform. If the input is unclear or lacking details, kindly ask for more information to give the most accurate and helpful response.
"""
)

app = Flask(__name__)
app.config['SECRET_KEY'] = 'secret!'
socketio = SocketIO(app, cors_allowed_origins="*")

def generate_response(user_input):
    """Generate AI response for user input"""
    try:
        chat_session = model.start_chat(history=[])
        response = chat_session.send_message(user_input)
        return response.text
    except Exception as e:
        return f"Error: {str(e)}"

@socketio.on('message')
def handle_message(msg):
    print(f"Received: {msg}")
    response = generate_response(msg)
    send(response)

if __name__ == '__main__':
    print("Starting chatbot server...")
    socketio.run(app, host='127.0.0.1', port=5000)