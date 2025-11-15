from flask import Flask, request, jsonify
import smtplib, random

app = Flask(__name__)

# In-memory store for OTPs
otp_store = {}

@app.route('/send-otp', methods=['POST'])
def send_otp():
    data = request.get_json()
    email = data['email']
    otp = str(random.randint(100000, 999999))
    otp_store[email] = otp

    # SMTP settings (from Brevo)
    smtp_server = "smtp-relay.brevo.com"
    smtp_port = 587
    smtp_user = "905f1d001@smtp-brevo.com"
    smtp_pass = "U6DydkNMVg4HI0aF"

    subject = "Your OTP for ShortMyLink.in"
    body = f"Your OTP is: {otp}. It expires in 5 minutes."

    message = f"Subject: {subject}\n\n{body}"

    try:
        with smtplib.SMTP(smtp_server, smtp_port) as server:
            server.starttls()
            server.login(smtp_user, smtp_pass)
            server.sendmail(smtp_user, email, message)
        return jsonify({"message": "OTP sent to your email"}), 200
    except Exception as e:
        return jsonify({"error": str(e)}), 500

@app.route('/verify-otp', methods=['POST'])
def verify_otp():
    data = request.get_json()
    email = data['email']
    otp = data['otp']

    if otp_store.get(email) == otp:
        del otp_store[email]
        return jsonify({"message": "OTP verified ✅"}), 200
    else:
        return jsonify({"message": "Invalid OTP ❌"}), 401

if __name__ == '__main__':
    app.run(debug=True)
