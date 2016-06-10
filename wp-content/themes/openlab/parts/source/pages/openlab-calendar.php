<div class="row">
    <div class="col-md-24">
        <div class="submenu">
            <div class="submenu-text pull-left bold">Calendar:</div>
            <ul class="nav nav-inline">

                <?php foreach ($menu_items as $item): ?>
                    <li class="<?php echo $item['class'] ?>" id="<?php echo $item['slug'] ?>-groups-li"><a href="<?php echo $item['link'] ?>"><?php echo $item['name'] ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<div id="openlabCalendar" class="calendar-wrapper">
    <?php echo eo_get_event_fullcalendar($args); ?>
</div>

<div id="bpeo-ical-download">
    <h3><?php echo __('Subscribe', 'bp-event-organiser'); ?></h3>
    <li><a class="bpeo-ical-link" href="{$link}"><span class="icon"></span><?php echo __('Download iCalendar file (Public)', 'bp-event-organiser'); ?></a></li>
</div>