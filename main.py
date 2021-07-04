def execute(acc_id):

    import cv2 as cv
    import numpy as np
    import tensorflow as tf
    gpu_devices = tf.config.experimental.list_physical_devices('GPU')
    if len(gpu_devices) > 0:
        tf.config.experimental.set_memory_growth(gpu_devices[0], True)
    import core.utils as utils
    from core.yolov4 import filter_boxes
    from tensorflow.python.saved_model import tag_constants
    from core.config import cfg
    from PIL import Image
    from tensorflow.compat.v1 import ConfigProto, InteractiveSession
    from deep_sort import preprocessing, nn_matching
    from deep_sort.detection import Detection
    from deep_sort.tracker import Tracker
    from tools import generate_detections
    import imutils
    from trackable import Tracking
    import eel
    import urllib, urllib3
    import json

    def update(account, count, capacity):

        http = urllib3.PoolManager()
        query = http.request('POST', 'http://localhost/cimo/web/update_count.php', fields={
            'account': account,
            'count': count,
            'cap': capacity
        })

        print(query.data)

    detector_model = 'tensorflow'

    encoder = generate_detections.create_box_encoder('../assets/mars-small128.pb', batch_size=1)

    metric = nn_matching.NearestNeighborDistanceMetric('cosine', 0.3, None)

    tracker = Tracker(metric=metric)

    config = ConfigProto()
    config.gpu_options.allow_growth = True
    session = InteractiveSession(config=config)

    if detector_model != 'tensorflow':

        print('00PS! model might not be correctly programmed')

    else:

        asset_model = tf.saved_model.load('../checkpoints/custom-416', tags=[tag_constants.SERVING])

        inference = asset_model.signatures['serving_default']

    video_src = cv.VideoCapture(0)

    frame_count = 0

    tracked_person = {}

    capacity_threshold = 0

    if capacity_threshold == 0:

        http = urllib3.PoolManager()
        query = http.request('POST', 'http://localhost/cimo/web/get_settings.php', fields={
            'id': acc_id
        })

        response = query.data

        json_data = response.decode('utf-8')

        my_dict = json.loads(json_data)

        cap = int(my_dict['max_limit'])

        capacity_threshold = int(my_dict['max_limit'])

        count_id = my_dict['count_id']

    eel.get_data_num(capacity_threshold)

    while True:

        _, frame = video_src.read()

        resized_frame = imutils.resize(frame, width=850, height=750)

        if frame is None:

            print('The video has ended, no more frames')

        else:

            frame = cv.cvtColor(resized_frame, cv.COLOR_BGR2RGB)

            image = Image.fromarray(frame)

        line_thresh = resized_frame.shape[0] / 2

        frame_size = frame.shape[:2]

        img_data = cv.resize(frame, (416, 416))

        img_data = img_data / 255

        img_data = img_data[np.newaxis, ...].astype(np.float32)

        if detector_model != 'tensorflow':

            print('00PS! model might not be correctly programmed')

        else:

            batch_data = tf.constant(img_data)

            prediction_bbox = inference(batch_data)

            for k, v in prediction_bbox.items():

                boxes = v[:, :, 0:4]

                prediction_score = v[:, :, 4:]

        boxes, scores, classes, valid_detections = tf.image.combined_non_max_suppression(
            boxes=tf.reshape(boxes, (tf.shape(boxes)[0], -1, 1, 4)),
            scores=tf.reshape(
                prediction_score, (tf.shape(prediction_score)[0], -1, tf.shape(prediction_score)[-1])),
            max_output_size_per_class=50,
            max_total_size=50,
            iou_threshold=0.1,
            score_threshold=0.45
        )

        objects = valid_detections.numpy()[0]
        bboxes = boxes.numpy()[0]
        bboxes = bboxes[0:int(objects)]
        scores = scores.numpy()[0]
        scores = scores[0:int(objects)]
        classes = classes.numpy()[0]
        classes = classes[0:int(objects)]

        original_height, original_weight, _ = frame.shape
        bboxes = utils.format_boxes(bboxes, original_height, original_weight)

        class_names = utils.read_class_names(cfg.YOLO.CLASSES)

        names = []

        delete_index = []

        for index in range(objects):

            class_index = int(classes[index])

            class_name = class_names[class_index]

            if class_name != 'person':

                delete_index.append(index)

            else:

                names.append(class_name)

        names = np.array(names)
        bboxes = np.delete(bboxes, delete_index, axis=0)
        scores = np.delete(scores, delete_index, axis=0)
        feats = encoder(frame, bboxes)

        dets = [Detection(bbox, score, class_name, feature)
                for bbox, score, class_name, feature in zip(bboxes, scores, names, feats)]

        bxs = np.array([d.tlwh for d in dets])
        scores = np.array([d.confidence for d in dets])
        classes = np.array([d.class_name for d in dets])
        indices = preprocessing.non_max_suppression(boxes=bxs, classes=classes, max_bbox_overlap=0.50, scores=scores)
        detections = [dets[index] for index in indices]

        tracker.predict()
        tracker.update(detections)

        for index in tracker.tracks:

            if not index.is_confirmed() or index.time_since_update > 1:

                continue

            bound_box = index.to_tlbr()

            person_id = index.track_id

            class_name = index.get_class()

            x1 = int(bound_box[0])
            y1 = int(bound_box[1])
            x2 = int(bound_box[2])
            y2 = int(bound_box[3])

            centroid = int((x1 + x2) / 2), int((y1 + y2) / 2)

            cv.circle(frame, centroid, 5, (255, 255, 255), -1)

            cv.rectangle(frame, (x1, y1), (x2, y2), (255, 255, 255), 2)

            tracer = tracked_person.get(person_id, None)

            if tracer is None:

                tracer = Tracking(person_id, centroid)

            else:

                y = [c[1] for c in tracer.centroids]

                y_coordinates = y[-1]

                tracer.centroids.append(centroid)

                if tracer.initial_movement is None:

                    if y_coordinates < line_thresh:

                        tracer.initial_movement = 'Going in'

                    elif y_coordinates > line_thresh:

                        tracer.initial_movement = 'Going out'

                if tracer.is_counted is False:

                    if tracer.initial_movement == 'Going in' and y_coordinates > line_thresh:

                        tracer.initial_movement = None

                        tracer.is_counted = True

                        capacity_threshold -= 1

                        update(account=count_id, count=capacity_threshold, capacity=cap)

                        eel.get_data_num(capacity_threshold)

                    elif tracer.initial_movement == 'Going out' and y_coordinates < line_thresh:

                        tracer.initial_movement = None

                        tracer.is_counted = True

                        capacity_threshold += 1

                        update(account=count_id, count=capacity_threshold, capacity=cap)

                        eel.get_data_num(capacity_threshold)

                elif tracer.is_counted is True:

                    if tracer.initial_movement == 'Going out' and y_coordinates < line_thresh:

                        tracer.initial_movement = None

                        tracer.is_counted = False

                        capacity_threshold += 1

                        update(account=count_id, count=capacity_threshold, capacity=cap)

                        eel.get_data_num(capacity_threshold)

                    elif tracer.initial_movement == 'Going in' and y_coordinates > line_thresh:

                        tracer.initial_movement = None

                        tracer.is_counted = False

                        capacity_threshold -= 1

                        update(account=count_id, count=capacity_threshold, capacity=cap)

                        eel.get_data_num(capacity_threshold)

                cv.putText(frame, 'IM : ' + str(tracer.initial_movement), (x1 + 10, y1 + 20), cv.FONT_HERSHEY_PLAIN,
                           1, (255, 255, 255), 2, cv.LINE_AA)

            tracked_person[person_id] = tracer

        cv.line(frame, (0, int(resized_frame.shape[0] / 2)),
                (resized_frame.shape[1], int(resized_frame.shape[0] / 2)), (255, 255, 255), 2)

        output_frame = np.asarray(frame)
        output_frame = cv.cvtColor(frame, cv.COLOR_BGR2RGB)

        cv.imshow('CIMO camera', output_frame)

        if cv.waitKey(10) & 0xff == ord('d'):

            break

    cv.destroyAllWindows()


