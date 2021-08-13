import eel
import main

eel.init('D:/xampp/htdocs/cimo_desktop')


# close
def close_callback_index(route, websockets):
    if not websockets:
        print('Closing websockets! ' + str(websockets))
        exit()


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
