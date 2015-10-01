<?php

return array(
    array(
        'title' => 'Profile Favorite Media',
        'description' => 'Displays favorite media entries on their profile (based on likes)',
        'category' => 'Media Plugin',
        'type' => 'widget',
        'name' => 'whmedia.profile-fmedia',
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
                                ),
                                array(
                                  'Text',
                                  'thumb_height',
                                  array(
                                    'label' => 'Thumb height (px)'
                                  )
                                )
                              )
                            ),
        'defaultParams' => array(
            'title' => 'My favorite media',
            'titleCount' => true,
            'thumb_width' => 160,
            'thumb_height' => 125,
            'itemCountPerPage' => 8
        )
    ),
    array(
        'title' => 'Profile Favorite Projects',
        'description' => 'Displays favorite media projects on their profile (based on likes).',
        'category' => 'Media Plugin',
        'type' => 'widget',
        'name' => 'whmedia.profile-fproject',
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
                                ),
                                array(
                                  'Text',
                                  'thumb_height',
                                  array(
                                    'label' => 'Thumb height (px)'
                                  )
                                )
                              )
                            ),
        'defaultParams' => array(
            'title' => 'My favorite media',
            'titleCount' => true,
            'thumb_width' => 160,
            'thumb_height' => 125,
            'itemCountPerPage' => 8
        )
    ),
    array(
        'title' => 'Profile Projects',
        'description' => 'Displays members projects on their profile.',
        'category' => 'Media Plugin',
        'type' => 'widget',
        'name' => 'whmedia.profile-project',
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
                                ),
                                array(
                                  'Text',
                                  'thumb_height',
                                  array(
                                    'label' => 'Thumb height (px)'
                                  )
                                )
                              )
                            ),
        'defaultParams' => array(
            'title' => 'My Media Projects',
            'titleCount' => true,
            'thumb_width' => 160,
            'thumb_height' => 125,
            'itemCountPerPage' => 8
            )
        ),
    array(
        'title' => 'Random or Newest Media',
        'description' => 'Displays random or newest media.',
        'category' => 'Media Plugin',
        'type' => 'widget',
        'name' => 'whmedia.media',
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
                                ),
                                array(
                                  'Text',
                                  'thumb_height',
                                  array(
                                    'label' => 'Thumb height (px)'
                                  )
                                ),
                                array(
                                  'Radio',
                                  'show_type',
                                  array(
                                    'label' => 'Show type',
                                    'multiOptions' => array('list' => 'List of items.',
                                                            'slider' => 'Slider of items.')
                                  )
                                ),
                                array(
                                  'Text',
                                  'slider_show_items',
                                  array(
                                    'label' => 'Amount of display projects',
                                    'validators' => array('Digits', array('validator' => 'Between', 'options' => array(1, 99)))
                                  )
                                )
                              )
                            ),
        'defaultParams' => array(
            'title' => 'Media',
            'titleCount' => false,
            'count_media' => 5,
            'show_media' => 'newest',
            'thumb_width' => 160,
            'thumb_height' => 125,
            'show_type' => 'list',
            'slider_show_items' => 3
        )
    ),
    array(
        'title' => 'Featured Media',
        'description' => 'Displays featured media based on likes by admin.',
        'category' => 'Media Plugin',
        'type' => 'widget',
        'name' => 'whmedia.featured-media',
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
                                ),
                                array(
                                  'Text',
                                  'thumb_height',
                                  array(
                                    'label' => 'Thumb height (px)'
                                  )
                                ),
                                array(
                                  'Radio',
                                  'show_type',
                                  array(
                                    'label' => 'Show type',
                                    'multiOptions' => array('list' => 'List of items.',
                                                            'slider' => 'Slider of items.')
                                  )
                                ),
                                array(
                                  'Text',
                                  'slider_show_items',
                                  array(
                                    'label' => 'Amount of display projects',
                                    'validators' => array('Digits', array('validator' => 'Between', 'options' => array(1, 99)))
                                  )
                                )
                              )
                            ),
        'defaultParams' => array(
            'title' => 'Featured Media',
            'titleCount' => false,
            'count_media' => 5,
            'thumb_width' => 160,
            'thumb_height' => 125,
            'show_type' => 'list',
            'slider_show_items' => 3
        )
    ),
    array(
        'title' => 'Most Popular Project',
        'description' => 'Displays most viewed media projects (today, this week, this month).',
        'category' => 'Media Plugin',
        'type' => 'widget',
        'name' => 'whmedia.popular-project',
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
                                ),
                                array(
                                  'Text',
                                  'thumb_height',
                                  array(
                                    'label' => 'Thumb height (px)'
                                  )
                                ),
                                array(
                                  'Radio',
                                  'show_type',
                                  array(
                                    'label' => 'Show type',
                                    'multiOptions' => array('list' => 'List of items.',
                                                            'slider' => 'Slider of items.')
                                  )
                                ),
                                array(
                                  'Text',
                                  'slider_show_items',
                                  array(
                                    'label' => 'Amount of display projects',
                                    'validators' => array('Digits', array('validator' => 'Between', 'options' => array(1, 99)))
                                  )
                                )
                              )
                            ),
        'defaultParams' => array(
            'title' => 'Media',
            'titleCount' => false,
            'count_media' => 5,
            'period_time' => 'week',
            'thumb_width' => 160,
            'thumb_height' => 125,
            'show_type' => 'list',
            'slider_show_items' => 3
        )
    ),
    array(
        'title' => 'Best Media',
        'description' => 'Displays the most popular media among members (today, this week, this month).It is based on likes.',
        'category' => 'Media Plugin',
        'type' => 'widget',
        'name' => 'whmedia.popular-media',
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
                                ),
                                array(
                                  'Text',
                                  'thumb_height',
                                  array(
                                    'label' => 'Thumb height (px)'
                                  )
                                ),
                                array(
                                  'Radio',
                                  'show_type',
                                  array(
                                    'label' => 'Show type',
                                    'multiOptions' => array('list' => 'List of items.',
                                                            'slider' => 'Slider of items.')
                                  )
                                ),
                                array(
                                  'Text',
                                  'slider_show_items',
                                  array(
                                    'label' => 'Amount of display projects',
                                    'validators' => array('Digits', array('validator' => 'Between', 'options' => array(1, 99)))
                                  )
                                )
                              )
                            ),
        'defaultParams' => array(
            'title' => 'Media',
            'titleCount' => false,
            'count_media' => 5,
            'period_time' => 'week',
            'thumb_width' => 160,
            'thumb_height' => 125,
            'show_type' => 'list',
            'slider_show_items' => 3
        )
    ),
    array(
        'title' => 'Tags',
        'description' => 'Displays popular media tags.',
        'category' => 'Media Plugin',
        'type' => 'widget',
        'name' => 'whmedia.tags',
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
                                  'count_item',
                                  array(
                                    'label' => 'Count of tags'
                                  )
                                )
                              )
                            ),
        'defaultParams' => array(
            'title' => 'Media Tags',
            'titleCount' => false,
            'count_item' => 5
        )
    ),
    array(
        'title' => 'Featured Projects',
        'description' => 'Displays featured projects based on likes by admin.',
        'category' => 'Media Plugin',
        'type' => 'widget',
        'name' => 'whmedia.featured-project',
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
                                ),
                                array(
                                  'Text',
                                  'thumb_height',
                                  array(
                                    'label' => 'Thumb height (px)'
                                  )
                                ),
                                array(
                                  'Radio',
                                  'show_type',
                                  array(
                                    'label' => 'Show type',
                                    'multiOptions' => array('list' => 'List of items.',
                                                            'slider' => 'Slider of items.')
                                  )
                                ),
                                array(
                                  'Text',
                                  'slider_show_items',
                                  array(
                                    'label' => 'Amount of display projects',
                                    'validators' => array('Digits', array('validator' => 'Between', 'options' => array(1, 99)))
                                  )
                                )
                              )
                            ),
        'defaultParams' => array(
            'title' => 'Featured Projects',
            'titleCount' => false,
            'count_media' => 5,
            'thumb_width' => 160,
            'thumb_height' => 125,
            'show_type' => 'list',
            'slider_show_items' => 3
        )
    ),
    array(
        'title' => 'Media Plugin Browse Menu',
        'description' => 'Displays a menu in the Media Plugin browse page.',
        'category' => 'Media Plugin',
        'type' => 'widget',
        'name' => 'whmedia.browse-menu',
        'requirements' => array(
          'no-subject',
        ),
    ),
    array(
        'title' => 'Media Plugin Browse Search',
        'description' => 'Displays a search form in the Media Plugin browse page.',
        'category' => 'Media Plugin',
        'type' => 'widget',
        'name' => 'whmedia.browse-search',
        'requirements' => array(
          'no-subject',
        ),
    ),
    array(
        'title' => 'Create a Project',
        'description' => 'Show link "Create a Project".',
        'category' => 'Media Plugin',
        'type' => 'widget',
        'name' => 'whmedia.create-project',
        'requirements' => array(
          'no-subject',
        ),
    ),
    array(
        'title' => "Projects Slider" ,
        'description' => 'Displays slider with user projects.',
        'category' => 'Media Plugin',
        'type' => 'widget',
        'name' => 'whmedia.projects-slider',
        'adminForm' => array('elements' => array(
                                array(
                                  'Text',
                                  'count_item',
                                  array(
                                    'label' => 'Amount of display projects',
                                    'validators' => array('Digits', array('validator' => 'Between', 'options' => array(1, 999)))
                                  )
                                )
                              )
                            ),
        'defaultParams' => array(
            'title' => 'User projects scroller',
            'titleCount' => false,
            'count_item' => 6
        )
    ),
    array(
        'title' => "Social Sharing" ,
        'description' => 'Displays Facebook, twitter, g+ buttons.',
        'category' => 'Media Plugin',
        'type' => 'widget',
        'name' => 'whmedia.share-social',

        'defaultParams' => array(
            'title' => "Social Sharing"
        )
    ),
    array(
        'title' => "Profile Follow" ,
        'description' => 'Displays on profile "Follow" block.',
        'category' => 'Media Plugin',
        'type' => 'widget',
        'name' => 'whmedia.profile-follow',
        'defaultParams' => array(
            'title' => ""
        )
    ),
    array(
        'title' => "Follow Suggestion" ,
        'description' => 'Follow Suggestion widget base on mutual follows.',
        'category' => 'Media Plugin',
        'type' => 'widget',
        'name' => 'whmedia.follow-suggestion',
        'defaultParams' => array(
            'title' => "You Might Also Like the Following Members"
        )
    ),
    array(
        'title' => "Project comments" ,
        'description' => 'View Project page comments',
        'category' => 'Media Plugin',
        'type' => 'widget',
        'name' => 'whmedia.comments',
        'defaultParams' => array(
            'title' => ""
        )
    ),
    array(
        'title' => "Related Projects Slider" ,
        'description' => 'Displays slider with related projects.',
        'category' => 'Media Plugin',
        'type' => 'widget',
        'name' => 'whmedia.related-projects-slider',
        'adminForm' => array('elements' => array(
                                array(
                                  'Text',
                                  'count_item',
                                  array(
                                    'label' => 'Amount of display projects',
                                    'validators' => array('Digits', array('validator' => 'Between', 'options' => array(1, 999)))
                                  )
                                )
                              )
                            ),
        'defaultParams' => array(
            'title' => 'Related Projects',
            'titleCount' => false,
            'count_item' => 6
        )
    )
)
?>
