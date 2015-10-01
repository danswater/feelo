<?php

return array(
    array(
        'title' => 'Profile Favorite Media (Masonry)',
        'description' => 'Displays favorite media entries on their profile (based on likes)',
        'category' => 'Media Masonry',
        'type' => 'widget',
        'name' => 'mediamasonry.profile-fmedia',
        'adminForm' => array('elements' => array(
                                array(
                                  'Text',
                                  'title',
                                  array(
                                    'label' => 'Title'
                                  )
                                ),
                                array(
                                  'Text',
                                  'itemCountPerPage',
                                  array(
                                    'label' => 'Number of projects'
                                  )
                                ),
                                array(
                                  'Text',
                                  'thumb_width',
                                  array(
                                    'label' => 'Thumb width (px)'
                                  )
                                )
                              )
                            ),
        'defaultParams' => array(
            'title' => 'My favorite media',
            'titleCount' => true,
            'thumb_width' => 160,
            'itemCountPerPage' => 8
        )
    ),
    array(
        'title' => 'Profile Favorite Projects (Masonry)',
        'description' => 'Displays favorite media projects on their profile (based on likes).',
        'category' => 'Media Masonry',
        'type' => 'widget',
        'name' => 'mediamasonry.profile-fproject',
        'adminForm' => array('elements' => array(
                                array(
                                  'Text',
                                  'title',
                                  array(
                                    'label' => 'Title'
                                  )
                                ),
                                array(
                                  'Text',
                                  'itemCountPerPage',
                                  array(
                                    'label' => 'Number of projects'
                                  )
                                ),
                                array(
                                  'Text',
                                  'thumb_width',
                                  array(
                                    'label' => 'Thumb width (px)'
                                  )
                                )
                              )
                            ),
        'defaultParams' => array(
            'title' => 'My favorite media',
            'titleCount' => true,
            'thumb_width' => 160,
            'itemCountPerPage' => 8
        )
    ),
    array(
        'title' => 'Profile Projects (Masonry)',
        'description' => 'Displays members projects on their profile.',
        'category' => 'Media Masonry',
        'type' => 'widget',
        'name' => 'mediamasonry.profile-project',
        'adminForm' => array('elements' => array(
                                array(
                                  'Text',
                                  'title',
                                  array(
                                    'label' => 'Title'
                                  )
                                ),
                                array(
                                  'Text',
                                  'itemCountPerPage',
                                  array(
                                    'label' => 'Number of projects'
                                  )
                                ),
                                array(
                                  'Text',
                                  'thumb_width',
                                  array(
                                    'label' => 'Thumb width (px)'
                                  )
                                )
                              )
                            ),
        'defaultParams' => array(
            'title' => 'My Media Projects',
            'titleCount' => true,
            'thumb_width' => 160,
            'itemCountPerPage' => 8
            )
        ),
    array(
        'title' => 'Random or Newest Media (Masonry)',
        'description' => 'Displays random or newest media.',
        'category' => 'Media Masonry',
        'type' => 'widget',
        'name' => 'mediamasonry.media',
        'adminForm' => array('elements' => array(
                                array(
                                  'Text',
                                  'title',
                                  array(
                                    'label' => 'Title'
                                  )
                                ),
                                array(
                                  'Text',
                                  'count_media',
                                  array(
                                    'label' => 'Number of media'
                                  )
                                ),
                                array(
                                  'Radio',
                                  'show_media',
                                  array(
                                    'label' => 'Random or Newest media',
                                    'multiOptions' => array('random' => 'Random media.',
                                                            'newest' => 'Newest media.'),
                                  )
                                ),
                                array(
                                  'Text',
                                  'thumb_width',
                                  array(
                                    'label' => 'Thumb width (px)'
                                  )
                                )
                              )
                            ),
        'defaultParams' => array(
            'title' => 'Media',
            'titleCount' => false,
            'count_media' => 5,
            'show_media' => 'newest',
            'thumb_width' => 160
        )
    ),
    array(
        'title' => 'Featured Media (Masonry)',
        'description' => 'Displays featured media based on likes by admin.',
        'category' => 'Media Masonry',
        'type' => 'widget',
        'name' => 'mediamasonry.featured-media',
        'adminForm' => array('elements' => array(
                                array(
                                  'Text',
                                  'title',
                                  array(
                                    'label' => 'Title'
                                  )
                                ),
                                array(
                                  'Text',
                                  'count_media',
                                  array(
                                    'label' => 'Number of media'
                                  )
                                ),
                                array(
                                  'Text',
                                  'thumb_width',
                                  array(
                                    'label' => 'Thumb width (px)'
                                  )
                                )
                              )
                            ),
        'defaultParams' => array(
            'title' => 'Featured Media',
            'titleCount' => false,
            'count_media' => 5,
            'thumb_width' => 160
        )
    ),
    array(
        'title' => 'Most Popular Project (Masonry)',
        'description' => 'Displays most viewed media projects (today, this week, this month).',
        'category' => 'Media Masonry',
        'type' => 'widget',
        'name' => 'mediamasonry.popular-project',
        'adminForm' => array('elements' => array(
                                array(
                                  'Text',
                                  'title',
                                  array(
                                    'label' => 'Title'
                                  )
                                ),
                                array(
                                  'Text',
                                  'count_media',
                                  array(
                                    'label' => 'Number of media'
                                  )
                                ),
                                array(
                                  'Radio',
                                  'period_time',
                                  array(
                                    'label' => 'Popular project during:',
                                    'multiOptions' => array('today' => 'Today.',
                                                            'week' => 'This week.',
                                                            'month' => 'This month.'),
                                  )
                                ),
                                array(
                                  'Text',
                                  'thumb_width',
                                  array(
                                    'label' => 'Thumb width (px)'
                                  )
                                )
                              )
                            ),
        'defaultParams' => array(
            'title' => 'Popular Projects',
            'titleCount' => false,
            'count_media' => 5,
            'period_time' => 'week',
            'thumb_width' => 160
        )
    ),
    array(
        'title' => 'Best Media (Masonry)',
        'description' => 'Displays the most popular media among members (today, this week, this month).It is based on likes.',
        'category' => 'Media Masonry',
        'type' => 'widget',
        'name' => 'mediamasonry.popular-media',
        'adminForm' => array('elements' => array(
                                array(
                                  'Text',
                                  'title',
                                  array(
                                    'label' => 'Title'
                                  )
                                ),
                                array(
                                  'Text',
                                  'count_media',
                                  array(
                                    'label' => 'Number of media'
                                  )
                                ),
                                array(
                                  'Radio',
                                  'period_time',
                                  array(
                                    'label' => 'Best media projects during:',
                                    'multiOptions' => array('today' => 'Today.',
                                                            'week' => 'This week.',
                                                            'month' => 'This month.'),
                                  )
                                ),
                                array(
                                  'Text',
                                  'thumb_width',
                                  array(
                                    'label' => 'Thumb width (px)'
                                  )
                                )
                              )
                            ),
        'defaultParams' => array(
            'title' => 'Media',
            'titleCount' => false,
            'count_media' => 5,
            'period_time' => 'week',
            'thumb_width' => 160
        )
    ),
    array(
        'title' => 'Featured Projects (Masonry)',
        'description' => 'Displays featured projects based on likes by admin.',
        'category' => 'Media Masonry',
        'type' => 'widget',
        'name' => 'mediamasonry.featured-project',
        'adminForm' => array('elements' => array(
                                array(
                                  'Text',
                                  'title',
                                  array(
                                    'label' => 'Title'
                                  )
                                ),
                                array(
                                  'Text',
                                  'count_media',
                                  array(
                                    'label' => 'Number of media'
                                  )
                                ),
                                array(
                                  'Text',
                                  'thumb_width',
                                  array(
                                    'label' => 'Thumb width (px)'
                                  )
                                )
                              )
                            ),
        'defaultParams' => array(
            'title' => 'Featured Projects',
            'titleCount' => false,
            'count_media' => 5,
            'thumb_width' => 160
        )
    ),
    array(
        'title' => 'Featured Projects Lazy (Masonry)',
        'description' => 'Displays featured projects based on likes by admin (lazy load).',
        'category' => 'Media Masonry',
        'type' => 'widget',
        'name' => 'mediamasonry.featured-project-lazy',
        'adminForm' => array('elements' => array(
                                array(
                                  'Text',
                                  'title',
                                  array(
                                    'label' => 'Title'
                                  )
                                ),
                                array(
                                  'Text',
                                  'count_media',
                                  array(
                                    'label' => 'Number of media'
                                  )
                                ),
                                array(
                                  'Text',
                                  'thumb_width',
                                  array(
                                    'label' => 'Thumb width (px)'
                                  )
                                )
                              )
                            ),
        'defaultParams' => array(
            'title' => 'Featured Projects',
            'titleCount' => false,
            'count_media' => 5,
            'thumb_width' => 160
        )
    ),
    array(
        'title' => 'Profile Favorite Projects (Masonry-lazy)',
        'description' => 'Displays favorite media projects on their profile (based on likes).',
        'category' => 'Media Masonry',
        'type' => 'widget',
        'name' => 'mediamasonry.profile-fproject-lazy',
        'adminForm' => array('elements' => array(
                                array(
                                  'Text',
                                  'title',
                                  array(
                                    'label' => 'Title'
                                  )
                                ),
                                array(
                                  'Text',
                                  'itemCountPerPage',
                                  array(
                                    'label' => 'Number of projects'
                                  )
                                ),
                                array(
                                  'Text',
                                  'thumb_width',
                                  array(
                                    'label' => 'Thumb width (px)'
                                  )
                                )
                              )
                            ),
        'defaultParams' => array(
            'title' => 'My favorite media',
            'titleCount' => true,
            'thumb_width' => 160,
            'itemCountPerPage' => 8
        )
    ),
    array(
        'title' => 'Profile Projects (Masonry-lazy)',
        'description' => 'Displays members projects on their profile.',
        'category' => 'Media Masonry',
        'type' => 'widget',
        'name' => 'mediamasonry.profile-project-lazy',
        'adminForm' => array('elements' => array(
                                array(
                                  'Text',
                                  'title',
                                  array(
                                    'label' => 'Title'
                                  )
                                ),
                                array(
                                  'Text',
                                  'itemCountPerPage',
                                  array(
                                    'label' => 'Number of projects'
                                  )
                                ),
                                array(
                                  'Text',
                                  'thumb_width',
                                  array(
                                    'label' => 'Thumb width (px)'
                                  )
                                )
                              )
                            ),
        'defaultParams' => array(
            'title' => 'My Media Projects',
            'titleCount' => true,
            'thumb_width' => 160,
            'itemCountPerPage' => 8
            )
        ),
    array(
        'title' => 'Profile LiveFeed',
        'description' => 'Displays members LiveFeed on their profile.',
        'category' => 'Media Masonry',
        'type' => 'widget',
        'name' => 'mediamasonry.profile-livefeed',
        'adminForm' => array('elements' => array(
                                array(
                                  'Text',
                                  'title',
                                  array(
                                    'label' => 'Title'
                                  )
                                ),
                                array(
                                  'Text',
                                  'itemCountPerPage',
                                  array(
                                    'label' => 'Number of projects'
                                  )
                                ),
                                array(
                                  'Text',
                                  'thumb_width',
                                  array(
                                    'label' => 'Thumb width (px)'
                                  )
                                )
                              )
                            ),
        'defaultParams' => array(
            'title' => 'My LiveFeed',
            'titleCount' => true,
            'thumb_width' => 160,
            'itemCountPerPage' => 8
            )
        ),
    array(
        'title' => 'Media Activity Feed',
        'description' => 'Displays members media activity feed.',
        'category' => 'Media Masonry',
        'type' => 'widget',
        'name' => 'mediamasonry.activity-feed-lazy',
        'adminForm' => array('elements' => array(
                                array(
                                  'Text',
                                  'title',
                                  array(
                                    'label' => 'Title'
                                  )
                                ),
                                array(
                                  'Text',
                                  'itemCountPerPage',
                                  array(
                                    'label' => 'Number of projects'
                                  )
                                ),
                                array(
                                  'Text',
                                  'thumb_width',
                                  array(
                                    'label' => 'Thumb width (px)'
                                  )
                                )
                              )
                            ),
        'defaultParams' => array(
            'title' => 'Activity Feed',
            'titleCount' => false,
            'thumb_width' => 160,
            'itemCountPerPage' => 8
            )
        )
)
?>
