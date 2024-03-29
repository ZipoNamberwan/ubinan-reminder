import pywhatkit
import requests

pywhatkit.sendwhatmsg_instantly("+6282236981385", "initiate sending ubinan reminder message...", 60, True, 10)

base_url = "https://ubinan.bpskabprobolinggo.com/api/"
response = requests.get(base_url + "message-today")
if response.status_code == 200:
    pywhatkit.sendwhatmsg_instantly("+6282236981385", "connection success", 60, True, 10)

    json_data = response.json()

    if isinstance(json_data, list):
        for element in json_data:
            pywhatkit.sendwhatmsg_instantly(element['phone_number'], element['message'], 45, True, 10)
        for element in json_data:
            re = requests.post(base_url + "sent-message", data={'receiver' : element['sent_to'],
                    'type' : element['type'],
                    'message' : element['message'],
                    'phone_number' : element['phone_number'],
                    'role' : element['role'],
                    'ids' : str(element['ids'])})
    else:
        pywhatkit.sendwhatmsg_instantly("+6282236981385", "sending ubinan reminder message failed", 60, True, 10)
else:
        pywhatkit.sendwhatmsg_instantly("+6282236981385", "sending ubinan reminder message failed", 60, True, 10)