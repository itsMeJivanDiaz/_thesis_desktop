import eel
import urllib3
import json

eel.init('D:/xampp/htdocs/cimo_desktop')


# close
def close_callback(route, websockets):
    if not websockets:
        print('Disconnecting websockets!')
        exit()


eel.start('app/index.html', host='localhost', port=8001, size=(1100, 670), position=(0, 0),
          close_callback=close_callback, cmdline_args=['--incognito'])
