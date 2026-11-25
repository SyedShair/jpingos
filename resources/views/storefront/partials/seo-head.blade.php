
{{--
|--------------------------------------------------------------------------
| Madras Cafe Nottingham - SEO Head
|--------------------------------------------------------------------------
| Include in <head>:
| @include('storefront.partials.seo-head')
|
| Per-page overrides:
| @section('meta_title', 'Menu | Madras Cafe Nottingham')
| @section('meta_description', 'Explore our Indian food menu in Nottingham.')
|--------------------------------------------------------------------------
--}}

@php
    $s = \App\Models\Setting::current();

    $brand = 'Madras Cafe';
    $city = 'Nottingham';
    $homeUrl = url('/');
    $canonical = url()->current();

    $menuUrl = \Illuminate\Support\Facades\Route::has('storefront.menu.index')
        ? route('storefront.menu.index')
        : $homeUrl;

    /*
    |--------------------------------------------------------------------------
    | Delivery areas
    |--------------------------------------------------------------------------
    | IMPORTANT: Keep only areas where you genuinely provide delivery.
    */

    $deliveryAreas = [
        'Lenton',
        'Beeston',
        'West Bridgford',
        'Arnold',
        'Carlton',
        'Sherwood',
        'Mapperley',
        'Radford',
        'Wollaton',
        'Sneinton',
        'Bulwell',
        'Bilborough',
        'Aspley',
    ];

    $deliveryPostcodes = [
        'NG1',
        'NG2',
        'NG3',
        'NG4',
        'NG5',
        'NG6',
        'NG7',
        'NG8',
        'NG9',
    ];

    /*
    |--------------------------------------------------------------------------
    | Default SEO metadata
    |--------------------------------------------------------------------------
    */

    $defaultTitle = 'Madras Cafe Nottingham | South Indian Restaurant & Indian Takeaway';

    $defaultDesc = 'Discover South Indian and Indian food at Madras Cafe Nottingham. Explore our menu, including dosa, idli, biryani and curries. Order online for delivery or collection.';

    $metaTitle = trim($__env->yieldContent('meta_title')) ?: $defaultTitle;

    $metaDesc = trim($__env->yieldContent('meta_description')) ?: $defaultDesc;

    /*
    |--------------------------------------------------------------------------
    | Images
    |--------------------------------------------------------------------------
    */

    $logoUrl = !empty($s->logo)
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($s->logo)
        : asset('storefront/assets/images/logo/logo.png');

    $ogImage = !empty($s->banner_one)
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($s->banner_one)
        : $logoUrl;

    /*
    |--------------------------------------------------------------------------
    | Cuisine and SEO keywords
    |--------------------------------------------------------------------------
    | Only include food and services actually offered by your restaurant.
    */

    $servesCuisine = [
        'Indian',
        'South Indian',
        'Chettinad',
        'Kerala',
        'Tamil',
        'Hyderabadi',
    ];

    $seoKeywords = [

        // Brand and Nottingham
        'Madras Cafe Nottingham',
        'Madras Cafe Indian restaurant Nottingham',
        'Indian takeaway Nottingham',
        'Indian restaurant Nottingham',
        'South Indian restaurant Nottingham',
        'South Indian takeaway Nottingham',
        'Indian food delivery Nottingham',
        'order Indian food online Nottingham',
        'Indian food collection Nottingham',

        // Local search
        'Indian takeaway Nottingham city',
        'Indian takeaway West Bridgford',
        'Indian takeaway Beeston',
        'Indian takeaway Lenton',
        'Indian takeaway Sherwood',
        'Indian takeaway Mapperley',
        'Indian takeaway Arnold',
        'Indian takeaway Carlton',
        'Indian takeaway Sneinton',
        'Indian takeaway Radford',
        'Indian takeaway Wollaton',
        'Indian takeaway Bilborough',
        'Indian takeaway NG1',
        'Indian takeaway NG2',
        'Indian takeaway NG3',
        'Indian takeaway NG4',
        'Indian takeaway NG5',
        'Indian takeaway NG6',
        'Indian takeaway NG7',
        'Indian takeaway NG8',
        'Indian takeaway NG9',

        // South Indian dishes
        'Dosa Nottingham',
        'Masala dosa Nottingham',
        'Plain dosa Nottingham',
        'Rava dosa Nottingham',
        'Egg dosa Nottingham',
        'Cheese dosa Nottingham',
        'Onion dosa Nottingham',
        'Podi dosa Nottingham',
        'Idli Nottingham',
        'Idli sambar Nottingham',
        'Medu vada Nottingham',
        'Uttapam Nottingham',
        'Pongal Nottingham',
        'Sambar Nottingham',
        'South Indian breakfast Nottingham',
        'South Indian thali Nottingham',

        // Biryani
        'Biryani Nottingham',
        'Chicken biryani Nottingham',
        'Lamb biryani Nottingham',
        'Mutton biryani Nottingham',
        'Vegetable biryani Nottingham',
        'Egg biryani Nottingham',
        'Hyderabadi biryani Nottingham',
        'Chicken dum biryani Nottingham',

        // Curries
        'Chicken curry Nottingham',
        'Chicken Chettinad Nottingham',
        'Mutton Chettinad Nottingham',
        'Madras curry Nottingham',
        'Chicken Madras Nottingham',
        'Lamb Madras Nottingham',
        'Butter chicken Nottingham',
        'Chicken tikka masala Nottingham',
        'Chicken korma Nottingham',
        'Chicken jalfrezi Nottingham',
        'Lamb rogan josh Nottingham',
        'Pepper chicken Nottingham',
        'Kerala curry Nottingham',

        // Vegetarian food
        'Vegetarian Indian food Nottingham',
        'Paneer curry Nottingham',
        'Palak paneer Nottingham',
        'Matar paneer Nottingham',
        'Kadai paneer Nottingham',
        'Chana masala Nottingham',
        'Dal makhani Nottingham',
        'Vegetable korma Nottingham',
        'Indian vegan food Nottingham',

        // Starters and snacks
        'Indian starters Nottingham',
        'Samosa Nottingham',
        'Onion bhaji Nottingham',
        'Chicken 65 Nottingham',
        'Paneer tikka Nottingham',
        'Pani puri Nottingham',
        'Bhel puri Nottingham',
        'Indian chaat Nottingham',
        'Aloo tikki Nottingham',
        'Pakora Nottingham',

        // Breads and sides
        'Garlic naan Nottingham',
        'Butter naan Nottingham',
        'Plain naan Nottingham',
        'Tandoori roti Nottingham',
        'Chapati Nottingham',
        'Paratha Nottingham',
        'Indian rice Nottingham',

        // Desserts and drinks
        'Gulab jamun Nottingham',
        'Kulfi Nottingham',
        'Rasmalai Nottingham',
        'Jalebi Nottingham',
        'Falooda Nottingham',
        'Mango lassi Nottingham',
        'Sweet lassi Nottingham',
        'Masala chai Nottingham',
        'Indian desserts Nottingham',

        // Ordering and services
        'Indian takeaway order online Nottingham',
        'Indian food collection Nottingham',
        'Indian food delivery Nottingham',
        'Indian restaurant menu Nottingham',
        'Indian family meal Nottingham',
        'Indian party catering Nottingham',
        'Indian office catering Nottingham',
    ];

    /*
    |--------------------------------------------------------------------------
    | Social media hashtags
    |--------------------------------------------------------------------------
    | These are for social media content, not Google ranking metadata.
    */

    $hashtags = [
        '#MadrasCafe',
        '#NottinghamFood',
        '#NottinghamFoodies',
        '#NottinghamIndianFood',
        '#IndianFood',
        '#SouthIndianFood',
        '#DosaLovers',
        '#BiryaniLovers',
        '#ChettinadFood',
        '#KeralaFood',
        '#TamilFood',
        '#IndianTakeaway',
        '#NottinghamTakeaway',
        '#NottinghamDelivery',
        '#MasalaDosa',
        '#ChickenBiryani',
        '#IndianCuisine',
        '#FreshlyCookedFood',
        '#EatLocalNottingham',
        '#CurryLovers',
    ];

    /*
    |--------------------------------------------------------------------------
    | Opening hours
    |--------------------------------------------------------------------------
    */

    $hours = [];

    foreach (($s->opening_hours ?? []) as $day => $h) {

        if (
            empty($h['closed']) &&
            !empty($h['open']) &&
            !empty($h['close'])
        ) {
            $hours[] = [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => 'https://schema.org/' . ucfirst(strtolower($day)),
                'opens' => $h['open'],
                'closes' => $h['close'],
            ];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Social profiles
    |--------------------------------------------------------------------------
    */

    $sameAs = array_values(array_filter([
        $s->instagram ?? null,
        $s->facebook ?? null,
    ]));

    /*
    |--------------------------------------------------------------------------
    | Restaurant structured data
    |--------------------------------------------------------------------------
    */

    $schema = array_filter([

        '@context' => 'https://schema.org',

        '@type' => 'Restaurant',

        '@id' => $homeUrl . '#restaurant',

        'name' => $brand,

        'description' => $defaultDesc,

        'url' => $homeUrl,

        'image' => [$ogImage],

        'logo' => $logoUrl,

        'telephone' => $s->phone ?? null,

        'email' => $s->email ?? null,

        'servesCuisine' => $servesCuisine,

        'keywords' => implode(', ', $seoKeywords),

        'priceRange' => '££',

        'hasMenu' => $menuUrl,

        'acceptsReservations' => true,

        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => $s->address ?? null,
            'addressLocality' => $city,
            'addressRegion' => 'Nottinghamshire',
            'addressCountry' => 'GB',
        ],

        'areaServed' => array_map(
            fn ($area) => [
                '@type' => 'Place',
                'name' => $area . ', Nottingham',
            ],
            array_merge([$city], $deliveryAreas)
        ),

        'openingHoursSpecification' => $hours ?: null,

        'sameAs' => $sameAs ?: null,

        'potentialAction' => [
            '@type' => 'OrderAction',
            'target' => [
                '@type' => 'EntryPoint',
                'urlTemplate' => $menuUrl,
                'actionPlatform' => [
                    'https://schema.org/DesktopWebPlatform',
                    'https://schema.org/MobileWebPlatform',
                ],
            ],
        ],

    ], fn ($value) => $value !== null && $value !== '');

    /*
    |--------------------------------------------------------------------------
    | FAQ structured data
    |--------------------------------------------------------------------------
    | Make sure these FAQs and answers are also visible on the actual page.
    */

    $faqs = [

        [
            'q' => 'Where can I find Madras Cafe in Nottingham?',
            'a' => 'You can find our restaurant address and contact details on the Madras Cafe website.',
        ],

        [
            'q' => 'What type of food does Madras Cafe serve?',
            'a' => 'Madras Cafe focuses on Indian cuisine, including South Indian food and the dishes available on our current menu.',
        ],

        [
            'q' => 'Can I order Indian food online in Nottingham?',
            'a' => 'Visit the Madras Cafe online menu to view the available dishes and ordering options.',
        ],

        [
            'q' => 'What is dosa?',
            'a' => 'Dosa is a traditional South Indian crepe commonly made from fermented rice and lentil batter. It is served with accompaniments such as sambar and chutney.',
        ],

        [
            'q' => 'What is the difference between South Indian and North Indian food?',
            'a' => 'South Indian cuisine commonly features rice-based dishes, dosa, idli and sambar. North Indian cuisine commonly features breads, rich curries and tandoori dishes.',
        ],

        [
            'q' => 'Does Madras Cafe offer takeaway?',
            'a' => 'Please check the online menu or contact Madras Cafe for current takeaway and collection availability.',
        ],

        [
            'q' => 'Does Madras Cafe offer food delivery?',
            'a' => 'Check your postcode during online checkout to see whether delivery is available in your area.',
        ],

        [
            'q' => 'Can I book a table at Madras Cafe?',
            'a' => 'Please contact Madras Cafe directly or check the website for current table booking availability.',
        ],

    ];

    $faqSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',

        'mainEntity' => array_map(
            fn ($f) => [
                '@type' => 'Question',
                'name' => $f['q'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $f['a'],
                ],
            ],
            $faqs
        ),
    ];

@endphp

{{-- ============================================================
     BASIC SEO
============================================================ --}}

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>{{ $metaTitle }}</title>

<meta name="description" content="{{ $metaDesc }}">

<meta name="keywords" content="{{ implode(', ', $seoKeywords) }}">

<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">

<link rel="canonical" href="{{ $canonical }}">

<link rel="alternate" hreflang="en-gb" href="{{ $canonical }}">

<link rel="alternate" hreflang="x-default" href="{{ $canonical }}">

<meta name="theme-color" content="#6B1118">

{{-- ============================================================
     LOCAL SEO
============================================================ --}}

<meta name="geo.region" content="GB-NGM">

<meta name="geo.placename" content="Nottingham">

{{-- ============================================================
     OPEN GRAPH
============================================================ --}}

<meta property="og:type" content="website">

<meta property="og:site_name" content="{{ $brand }}">

<meta property="og:locale" content="en_GB">

<meta property="og:title" content="{{ $metaTitle }}">

<meta property="og:description" content="{{ $metaDesc }}">

<meta property="og:url" content="{{ $canonical }}">

<meta property="og:image" content="{{ $ogImage }}">

<meta property="og:image:alt" content="{{ $brand }} - Indian restaurant in {{ $city }}">

{{-- ============================================================
     TWITTER / X
============================================================ --}}

<meta name="twitter:card" content="summary_large_image">

<meta name="twitter:title" content="{{ $metaTitle }}">

<meta name="twitter:description" content="{{ $metaDesc }}">

<meta name="twitter:image" content="{{ $ogImage }}">

{{-- ============================================================
     RESTAURANT SCHEMA
============================================================ --}}

<script type="application/ld+json">
{!! json_encode(
    $schema,
    JSON_UNESCAPED_SLASHES |
    JSON_UNESCAPED_UNICODE |
    JSON_HEX_TAG |
    JSON_PRETTY_PRINT
) !!}
</script>

{{-- ============================================================
     FAQ SCHEMA
============================================================ --}}

<script type="application/ld+json">
{!! json_encode(
    $faqSchema,
    JSON_UNESCAPED_SLASHES |
    JSON_UNESCAPED_UNICODE |
    JSON_HEX_TAG |
    JSON_PRETTY_PRINT
) !!}
</script>
