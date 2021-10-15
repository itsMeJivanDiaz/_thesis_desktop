import eel
import sys
import os
import inspect

current = os.path.dirname(os.path.abspath(inspect.getfile(inspect.currentframe())))
pardir = os.path.dirname(current)
sys.path.insert(0, pardir)

import main

eel.init('D:/xampp/htdocs/cimo_desktop')

# close
def close_callback_index(route, websockets):
    if not websockets:
        print('Closing websockets! ' + str(websockets))


@eel.expose
def close_window():
    eel.update_status(0)


@eel.expose
def start_extended():
    eel.show('app/extended_display.html')


@eel.expose
def start_cam(acc_id, status):
    main.execute(acc_id, status)


eel.start('app/index.html', host='localhost', port=8001, size=(1100, 715), position=(0, 0),
          close_callback=close_callback_index, cmdline_args=['--incognito'])
