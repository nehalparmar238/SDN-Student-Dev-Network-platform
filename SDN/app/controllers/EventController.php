<?php

class EventController extends Controller
{
    // Show upcoming and past events with an optional type filter.
    public function index()
    {
        $eventType = $this->getEventType();
        $eventModel = new Event();

        $this->render('events/index', array(
            'eventType' => $eventType,
            'upcomingEvents' => $eventModel->findUpcoming($eventType),
            'pastEvents' => $eventModel->findPast($eventType)
        ));
    }

    // Display one event and the current member's registration options.
    public function view()
    {
        $eventId = $this->getQueryId('id');
        $eventModel = new Event();
        $event = $eventId > 0 ? $eventModel->findById($eventId) : false;

        if (!$event) {
            $this->showNotFound();
            return;
        }

        $registrationModel = new EventRegistration();
        $isRegistered = isLoggedIn()
            ? $registrationModel->exists($eventId, currentUserId())
            : false;
        $isFuture = $this->isFutureEvent($event);
        $spotsLeft = null;
        $isFull = false;

        if ($event['capacity'] !== null) {
            $spotsLeft = max(
                0,
                (int) $event['capacity'] - (int) $event['registration_count']
            );
            $isFull = $spotsLeft < 1;
        }

        $this->render('events/view', array(
            'event' => $event,
            'isRegistered' => $isRegistered,
            'isFuture' => $isFuture,
            'isFull' => $isFull,
            'spotsLeft' => $spotsLeft
        ));
    }

    // Show events registered by the signed-in member.
    public function mine()
    {
        $this->requireLogin();

        $registrationModel = new EventRegistration();
        $events = $registrationModel->findByUser(currentUserId());

        foreach ($events as $index => $event) {
            $events[$index]['is_future'] = $this->isFutureEvent($event);
        }

        $this->render('events/mine', array(
            'events' => $events
        ));
    }

    // Register the signed-in member for a future event with available capacity.
    public function register()
    {
        $this->requireLogin();
        $this->requirePost();

        $eventId = $this->postId('event_id');
        $eventModel = new Event();
        $event = $eventId > 0 ? $eventModel->findById($eventId) : false;

        if (!$event) {
            setFlash('error', 'The event could not be found.');
            $this->redirect('events');
        }

        if (!$this->isFutureEvent($event)) {
            setFlash('error', 'Registration is only available for future events.');
            $this->redirectToEvent($eventId);
        }

        $registrationModel = new EventRegistration();

        if ($registrationModel->exists($eventId, currentUserId())) {
            setFlash('error', 'You are already registered for this event.');
            $this->redirectToEvent($eventId);
        }

        $registrationCount = $registrationModel->countForEvent($eventId);
        if (
            $event['capacity'] !== null
            && $registrationCount >= (int) $event['capacity']
        ) {
            setFlash('error', 'This event is full.');
            $this->redirectToEvent($eventId);
        }

        if (!$registrationModel->create($eventId, currentUserId())) {
            setFlash('error', 'You are already registered for this event.');
            $this->redirectToEvent($eventId);
        }

        $notificationModel = new Notification();
        $notificationModel->create(
            currentUserId(),
            'You registered for ' . $event['title'] . '.'
        );

        setFlash('success', 'You are registered for this event.');
        $this->redirectToEvent($eventId);
    }

    // Unregister the signed-in member from a future event.
    public function unregister()
    {
        $this->requireLogin();
        $this->requirePost();

        $eventId = $this->postId('event_id');
        $eventModel = new Event();
        $event = $eventId > 0 ? $eventModel->findById($eventId) : false;

        if (!$event) {
            setFlash('error', 'The event could not be found.');
            $this->redirect('events', 'mine');
        }

        if (!$this->isFutureEvent($event)) {
            setFlash('error', 'Past event registrations cannot be removed.');
            $this->redirectToEvent($eventId);
        }

        $registrationModel = new EventRegistration();
        if (!$registrationModel->delete($eventId, currentUserId())) {
            setFlash('error', 'You are not registered for this event.');
            $this->redirectToEvent($eventId);
        }

        setFlash('success', 'Your event registration has been removed.');
        $this->redirectToEvent($eventId);
    }

    // Return a valid event type filter or an empty string for all types.
    private function getEventType()
    {
        if (!isset($_GET['type']) || !is_string($_GET['type'])) {
            return '';
        }

        $eventType = trim($_GET['type']);
        $validTypes = array('event', 'workshop', 'hackathon');

        return in_array($eventType, $validTypes, true) ? $eventType : '';
    }

    // Read a positive event ID from the query string.
    private function getQueryId($field)
    {
        if (!isset($_GET[$field]) || !is_string($_GET[$field])) {
            return 0;
        }

        $value = filter_var(trim($_GET[$field]), FILTER_VALIDATE_INT);

        if ($value === false || $value < 1) {
            return 0;
        }

        return (int) $value;
    }

    // Read a positive event ID from a submitted form field.
    private function postId($field)
    {
        if (!isset($_POST[$field]) || !is_string($_POST[$field])) {
            return 0;
        }

        $value = filter_var(trim($_POST[$field]), FILTER_VALIDATE_INT);

        if ($value === false || $value < 1) {
            return 0;
        }

        return (int) $value;
    }

    // Check whether an event's date is still in the future.
    private function isFutureEvent($event)
    {
        $eventTimestamp = strtotime((string) $event['event_date']);

        return $eventTimestamp !== false && $eventTimestamp > time();
    }

    // Redirect to an event detail page when its ID is valid.
    private function redirectToEvent($eventId)
    {
        if ($eventId > 0) {
            $this->redirect('events', 'view', array('id' => (int) $eventId));
        }

        $this->redirect('events');
    }

    // Render the shared not-found page with its correct HTTP status.
    private function showNotFound()
    {
        http_response_code(404);
        $this->render('errors/404');
    }
}