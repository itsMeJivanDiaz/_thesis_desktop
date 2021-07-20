import eel
import urllib, urllib3
import json
import main

eel.init('D:/xampp/htdocs/cimo_desktop')

# register
@eel.expose
def get_form_data_register(data):
    http = urllib3.PoolManager()
    query = http.request('POST', 'http://localhost/cimo_desktop/app/register.php', fields={
         'name': str(data['est_name']['value']),
         'city': str(data['city']['value']),
         'branch': str(data['branch']['value']),
         'brgy': str(data['brgy']['value']),
         'latlong': str(data['latlong']['value']),
         'type': str(data['type']['value']),
         'username': str(data['username']['value']),
         'password': str(data['password']['value']),
    })

    response = query.data

    print(response)

    if response.decode('utf-8') is not None:
        eel.read_status_py(response.decode('utf-8'))

@eel.expose
def get_form_data_login(data):
    http = urllib3.PoolManager()
    query = http.request('POST', 'http://localhost/cimo_desktop/app/login.php', fields={
        'user-login': str(data['user-login']['value']),
        'pass-login': str(data['pass-login']['value'])
    })

    response = query.data

    print(response)

    my_json = response.decode('utf-8')

    my_dict = json.loads(my_json)

    if my_dict is not None:
        eel.read_status_login_py(my_dict)


eel.start('app/index.html', port=8001, size=(1000, 660), position=(0, 0),
          cmdline_args=['--incognito', '--no-experiments'])
