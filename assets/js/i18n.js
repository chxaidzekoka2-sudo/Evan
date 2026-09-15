/**
 * ====================================================================
 * i18n.js — ყველა ტექსტი სამივე ენაზე (KA / EN / RU).
 *
 * როგორ მუშაობს:
 *  - ყოველ HTML ელემენტს, რომლის ტექსტიც უნდა ითარგმნოს, აქვს
 *    data-i18n="key" ატრიბუტი (იხილე index.html).
 *  - applyLanguage() გადის ყველა ასეთ ელემენტზე და წერს textContent-ს
 *    ქვემოთ მოცემული ლექსიკონიდან, არჩეული ენის მიხედვით.
 *  - არჩეული ენა ინახება localStorage-ში, რომ გვერდის განახლების
 *    შემდეგაც დარჩეს იგივე.
 *
 * PHP-სთან დაკავშირებისას:
 *  - ეს ფაილი შეგიძლია საერთოდ არ შეეხო — JS-ით მუშაობს დამოუკიდებლად.
 *  - თუ გინდა სერვერზეც იცოდე მომხმარებლის ენა (მაგ. admin panel-ის
 *    კონტენტისთვის), გამოიყენე იგივე 'ka'/'en'/'ru' კოდები PHP მხარეზეც,
 *    რომ ბაზის სვეტების სახელები (name_ka, name_en, name_ru) დაემთხვეს.
 * ====================================================================
 */

const TRANSLATIONS = {
  ka: {
    site_name: 'Evan',
    announcement: 'უფასო მიწოდება საქართველოში — პირველ შეკვეთაზე 10% ფასდაკლება კოდით: EVAN10',
    nav_men: 'მამაკაცის',
    nav_women: 'ქალის',
    nav_shoes: 'ფეხსაცმელი',
    nav_accessories: 'აქსესუარები',
    nav_contact: 'კონტაქტი',
    search_placeholder: 'ძებნა...',
    hero_title: 'სტილი, რომელიც სვლას განსაზღვრავს',
    hero_sub: 'ახალი სეზონის კოლექცია — ტანსაცმელი და ფეხსაცმელი ერთად',
    shop_now: 'ნახეთ კოლექცია',
    categories_title: 'კატეგორიები',
    cat_men: 'მამაკაცის ტანსაცმელი',
    cat_women: 'ქალის ტანსაცმელი',
    cat_shoes: 'ფეხსაცმელი',
    cat_accessories: 'აქსესუარები',
    view_all: 'ყველას ნახვა',
    new_in_title: 'სიახლეები',
    select_size: 'აირჩიეთ ზომა',
    add_to_cart: 'კალათაში დამატება',
    added_msg: 'დაემატა კალათაში',
    story_text: 'ჩვენ ვირჩევთ ხარისხიან ტანსაცმელსა და ფეხსაცმელს, რომელიც კომფორტსა და სტილს აერთიანებს ყოველდღიურ ცხოვრებაში.',
    about_us: 'ჩვენს შესახებ',
    press_title: 'გვახსენებენ',
    instagram_title: 'გამოგვყევით Instagram-ზე',
    newsletter_title: 'შემოგვიერთდით',
    newsletter_sub: 'გამორჩეული სიახლეები და მისალმების ფასდაკლება',
    newsletter_placeholder: 'თქვენი ელ-ფოსტა',
    sign_up: 'გამოწერა',
    footer_info: 'ინფორმაცია',
    footer_returns: 'დაბრუნების პოლიტიკა',
    footer_privacy: 'კონფიდენციალურობა',
    footer_terms: 'წესები და პირობები',
    footer_shipping: 'მიწოდების პირობები',
    footer_help: 'დახმარება',
    footer_profile: 'პროფილი',
    footer_size_guide: 'ზომების გზამკვლევი',
    footer_stores: 'მაღაზიები',
    footer_tagline: 'სტილი, რომელიც სიმარტივეს ეხმიანება',
    rights: 'ყველა უფლება დაცულია.',
    cart_title: 'ჩემი კალათა',
    cart_empty: 'კალათა ცარიელია',
    subtotal: 'ჯამი',
    checkout: 'შეკვეთის გაფორმება',
    continue_shopping: 'ყიდვის გაგრძელება',
    promo_title: 'კეთილი იყოს თქვენი მობრძანება',
    promo_text: 'გამოიყენეთ კოდი პირველ შეკვეთაზე 10% ფასდაკლებისთვის',
    copy_code: 'კოპირება',
    close: 'დახურვა',
    free_delivery: 'უფასო მიწოდება',
    duties_included: 'გადასახადები ჩართულია',
    easy_returns: 'მარტივი დაბრუნება',
    filter_title: 'ფილტრი',
    filter_category: 'კატეგორია',
    filter_size: 'ზომა',
    filter_price: 'ფასი',
    filter_apply: 'გამოყენება',
    filter_clear: 'გასუფთავება',
    filter_all: 'ყველა',
    about_page_body: 'Evan 2026 წელს დაარსდა, როგორც პატარა ონლაინ მაღაზია საქართველოში — მიზნით, შემოგვეთავაზებინა ხარისხიანი, ყოველდღიური ტანსაცმელი და ფეხსაცმელი, სამართლიან ფასად. ჩვენ ვირჩევთ თითოეულ ნივთს პირადად, ვცდილობთ კომფორტისა და სტილის ბალანსი ვიპოვოთ. მადლობა რომ გვირჩევთ.',
    returns_body: 'დაბრუნება შესაძლებელია ნივთის მიღებიდან 14 დღის განმავლობაში, თუ ეტიკეტი დაცულია და ნივთი არ ყოფილა გამოყენებული. დასაბრუნებლად დაგვიკავშირდი WhatsApp-ზე ან ელ-ფოსტით, ჩვენც მოგცემთ ინსტრუქციას. თანხის დაბრუნება ხდება იმავე გადახდის მეთოდზე, დაბრუნებული ნივთის მიღებიდან 5-10 სამუშაო დღეში.',
    privacy_body: 'ჩვენ ვაგროვებთ მხოლოდ იმ პერსონალურ მონაცემებს, რაც აუცილებელია შეკვეთის დასამუშავებლად — სახელი, საკონტაქტო ინფორმაცია და მიწოდების მისამართი. ეს ინფორმაცია არ გადაეცემა მესამე მხარეს, გარდა მიწოდებისა და გადახდის პროვაიდერებისა, რომლებიც აუცილებელია შეკვეთის შესასრულებლად.',
    terms_body: 'საიტის გამოყენებით თანხმდები, რომ ყველა ინფორმაცია პროდუქტების შესახებ ზუსტია ჩვენი საუკეთესო ცოდნით, ფასები მითითებულია ლარში (GEL) და შეიძლება შეიცვალოს წინასწარი გაფრთხილების გარეშე. შეკვეთის განთავსებით ადასტურებ, რომ მოცემული საკონტაქტო ინფორმაცია ზუსტია.',
    shipping_body: 'მიწოდება საქართველოს მასშტაბით უფასოა. მიწოდების ვადა თბილისში 1-2 სამუშაო დღეა, რეგიონებში — 2-4 დღე. შეკვეთის დამუშავება იწყება გადახდის დადასტურების შემდეგ.',
    sizeguide_body: 'ტანსაცმლის ზომები: XS (44), S (46), M (48), L (50), XL (52). ფეხსაცმლის ზომები ევროპულ სტანდარტშია (36-44). თუ ორ ზომას შორის ხარ არჩევანში, გირჩევთ დიდი ზომის აღებას.',
    stores_body: 'ამჟამად Evan მხოლოდ ონლაინ მუშაობს — ფიზიკური მაღაზია ჯერ არ გვაქვს. კითხვების შემთხვევაში დაგვიკავშირდი WhatsApp-ზე ან ელ-ფოსტით.',
    contact_intro: 'გაქვს შეკითხვა? დაგვიწერე და მალე დაგიკავშირდებით.',
    contact_name: 'სახელი',
    contact_email_label: 'ელ-ფოსტა',
    contact_message: 'შეტყობინება',
    contact_send: 'გაგზავნა',
    contact_success: 'მადლობა! შენი შეტყობინება მივიღეთ.',
    profile_body: 'პროფილის გვერდი მალე დაემატება — მალე შეძლებ შენი შეკვეთების ისტორიის ნახვას აქედან.'
  },

  en: {
    site_name: 'Evan',
    announcement: 'Free delivery in Georgia — 10% off your first order with code: EVAN10',
    nav_men: 'Men',
    nav_women: 'Women',
    nav_shoes: 'Shoes',
    nav_accessories: 'Accessories',
    nav_contact: 'Contact',
    search_placeholder: 'Search...',
    hero_title: 'Style That Defines Every Step',
    hero_sub: 'The new season collection — clothing and footwear together',
    shop_now: 'Shop Now',
    categories_title: 'Categories',
    cat_men: "Men's Clothing",
    cat_women: "Women's Clothing",
    cat_shoes: 'Shoes',
    cat_accessories: 'Accessories',
    view_all: 'View All',
    new_in_title: 'New In',
    select_size: 'Select Size',
    add_to_cart: 'Add to Cart',
    added_msg: 'Added to cart',
    story_text: 'We curate quality clothing and footwear that bring together comfort and style for everyday life.',
    about_us: 'About Us',
    press_title: 'As Featured In',
    instagram_title: 'Follow us on Instagram',
    newsletter_title: 'Join our community',
    newsletter_sub: 'Exclusive updates and a welcome discount',
    newsletter_placeholder: 'Your email',
    sign_up: 'Sign Up',
    footer_info: 'Info',
    footer_returns: 'Returns Policy',
    footer_privacy: 'Privacy Policy',
    footer_terms: 'Terms of Service',
    footer_shipping: 'Shipping Policy',
    footer_help: 'Help',
    footer_profile: 'Profile',
    footer_size_guide: 'Size Guide',
    footer_stores: 'Stores',
    footer_tagline: 'Where Style Meets Ease',
    rights: 'All rights reserved.',
    cart_title: 'My Cart',
    cart_empty: 'Your cart is empty',
    subtotal: 'Subtotal',
    checkout: 'Checkout Securely',
    continue_shopping: 'Continue Shopping',
    promo_title: 'Welcome to Evan',
    promo_text: 'Use the code below to get 10% off your first order',
    copy_code: 'Copy',
    close: 'Close',
    free_delivery: 'Free Delivery',
    duties_included: 'Duties Included',
    easy_returns: 'Easy Returns',
    filter_title: 'Filter',
    filter_category: 'Category',
    filter_size: 'Size',
    filter_price: 'Price',
    filter_apply: 'Apply',
    filter_clear: 'Clear',
    filter_all: 'All',
    about_page_body: 'Evan was founded in 2026 as a small online store in Georgia — with the goal of offering quality, everyday clothing and footwear at a fair price. We personally select every item, aiming to balance comfort and style. Thank you for shopping with us.',
    returns_body: 'Returns are accepted within 14 days of receiving your item, provided the tags are intact and the item is unworn. To start a return, contact us via WhatsApp or email and we\'ll send you instructions. Refunds are issued to the original payment method within 5-10 business days of us receiving the returned item.',
    privacy_body: 'We only collect the personal data necessary to process your order — name, contact details, and delivery address. This information is not shared with third parties, except for delivery and payment providers required to fulfil your order.',
    terms_body: 'By using this site you agree that all product information is accurate to the best of our knowledge, prices are listed in GEL and may change without prior notice. By placing an order, you confirm that the contact details provided are accurate.',
    shipping_body: 'Delivery within Georgia is free. Delivery time is 1-2 business days in Tbilisi, and 2-4 days in other regions. Order processing begins once payment is confirmed.',
    sizeguide_body: 'Clothing sizes: XS (44), S (46), M (48), L (50), XL (52). Shoe sizes are in EU standard (36-44). If you\'re between two sizes, we recommend sizing up.',
    stores_body: 'Evan currently operates online only — we don\'t have a physical store yet. For any questions, reach out via WhatsApp or email.',
    contact_intro: 'Have a question? Send us a message and we\'ll get back to you soon.',
    contact_name: 'Name',
    contact_email_label: 'Email',
    contact_message: 'Message',
    contact_send: 'Send',
    contact_success: 'Thank you! We\'ve received your message.',
    profile_body: 'A profile page is coming soon — you\'ll soon be able to view your order history here.'
  },

  ru: {
    site_name: 'Evan',
    announcement: 'Бесплатная доставка по Грузии — скидка 10% на первый заказ по коду: EVAN10',
    nav_men: 'Мужское',
    nav_women: 'Женское',
    nav_shoes: 'Обувь',
    nav_accessories: 'Аксессуары',
    nav_contact: 'Контакты',
    search_placeholder: 'Поиск...',
    hero_title: 'Стиль, который определяет каждый шаг',
    hero_sub: 'Коллекция нового сезона — одежда и обувь вместе',
    shop_now: 'Смотреть коллекцию',
    categories_title: 'Категории',
    cat_men: 'Мужская одежда',
    cat_women: 'Женская одежда',
    cat_shoes: 'Обувь',
    cat_accessories: 'Аксессуары',
    view_all: 'Смотреть все',
    new_in_title: 'Новинки',
    select_size: 'Выберите размер',
    add_to_cart: 'В корзину',
    added_msg: 'Добавлено в корзину',
    story_text: 'Мы отбираем качественную одежду и обувь, сочетающую комфорт и стиль для повседневной жизни.',
    about_us: 'О нас',
    press_title: 'О нас пишут',
    instagram_title: 'Подписывайтесь на нас в Instagram',
    newsletter_title: 'Присоединяйтесь к нам',
    newsletter_sub: 'Эксклюзивные новости и приветственная скидка',
    newsletter_placeholder: 'Ваш email',
    sign_up: 'Подписаться',
    footer_info: 'Информация',
    footer_returns: 'Политика возврата',
    footer_privacy: 'Политика конфиденциальности',
    footer_terms: 'Условия использования',
    footer_shipping: 'Условия доставки',
    footer_help: 'Помощь',
    footer_profile: 'Профиль',
    footer_size_guide: 'Таблица размеров',
    footer_stores: 'Магазины',
    footer_tagline: 'Стиль, который прост в жизни',
    rights: 'Все права защищены.',
    cart_title: 'Моя корзина',
    cart_empty: 'Ваша корзина пуста',
    subtotal: 'Итого',
    checkout: 'Оформить заказ',
    continue_shopping: 'Продолжить покупки',
    promo_title: 'Добро пожаловать в Evan',
    promo_text: 'Используйте код ниже, чтобы получить скидку 10% на первый заказ',
    copy_code: 'Копировать',
    close: 'Закрыть',
    free_delivery: 'Бесплатная доставка',
    duties_included: 'Пошлины включены',
    easy_returns: 'Лёгкий возврат',
    filter_title: 'Фильтр',
    filter_category: 'Категория',
    filter_size: 'Размер',
    filter_price: 'Цена',
    filter_apply: 'Применить',
    filter_clear: 'Сбросить',
    filter_all: 'Все',
    about_page_body: 'Evan был основан в 2026 году как небольшой онлайн-магазин в Грузии — с целью предложить качественную повседневную одежду и обувь по справедливой цене. Мы лично отбираем каждую вещь, стремясь к балансу комфорта и стиля. Спасибо, что выбираете нас.',
    returns_body: 'Возврат возможен в течение 14 дней с момента получения товара, при условии сохранности бирок и отсутствия следов использования. Чтобы оформить возврат, свяжитесь с нами через WhatsApp или email — мы вышлем инструкции. Возврат средств осуществляется тем же способом оплаты в течение 5-10 рабочих дней после получения возвращённого товара.',
    privacy_body: 'Мы собираем только те персональные данные, которые необходимы для обработки заказа — имя, контактные данные и адрес доставки. Эта информация не передаётся третьим лицам, за исключением служб доставки и платёжных провайдеров, необходимых для выполнения заказа.',
    terms_body: 'Используя сайт, вы соглашаетесь с тем, что вся информация о товарах точна в меру наших знаний, цены указаны в лари (GEL) и могут изменяться без предварительного уведомления. Оформляя заказ, вы подтверждаете, что указанные контактные данные верны.',
    shipping_body: 'Доставка по Грузии бесплатна. Срок доставки в Тбилиси — 1-2 рабочих дня, в регионах — 2-4 дня. Обработка заказа начинается после подтверждения оплаты.',
    sizeguide_body: 'Размеры одежды: XS (44), S (46), M (48), L (50), XL (52). Размеры обуви в европейском стандарте (36-44). Если вы между двумя размерами, рекомендуем выбрать больший.',
    stores_body: 'В настоящее время Evan работает только онлайн — физического магазина пока нет. По любым вопросам обращайтесь через WhatsApp или email.',
    contact_intro: 'Есть вопрос? Напишите нам, и мы скоро ответим.',
    contact_name: 'Имя',
    contact_email_label: 'Email',
    contact_message: 'Сообщение',
    contact_send: 'Отправить',
    contact_success: 'Спасибо! Мы получили ваше сообщение.',
    profile_body: 'Страница профиля скоро появится — вы сможете видеть историю своих заказов здесь.'
  }
};

const LANG_KEY = 'evan_lang';
const SUPPORTED_LANGS = ['ka', 'en', 'ru'];

function getCurrentLang() {
  const stored = localStorage.getItem(LANG_KEY);
  return SUPPORTED_LANGS.includes(stored) ? stored : 'ka';
}

function setLanguage(lang) {
  if (!TRANSLATIONS[lang]) return;
  localStorage.setItem(LANG_KEY, lang);
  applyLanguage();
}

// ტექსტების ჩასმა: <span data-i18n="hero_title"></span> -> სათარგმნი ტექსტი
function applyLanguage() {
  const lang = getCurrentLang();
  const dict = TRANSLATIONS[lang];

  document.documentElement.lang = lang;

  document.querySelectorAll('[data-i18n]').forEach(el => {
    const key = el.getAttribute('data-i18n');
    if (dict[key] !== undefined) el.textContent = dict[key];
  });

  // placeholder-ებისთვის ცალკე ატრიბუტი: data-i18n-placeholder="key"
  document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
    const key = el.getAttribute('data-i18n-placeholder');
    if (dict[key] !== undefined) el.setAttribute('placeholder', dict[key]);
  });

  // მიმდინარე ენის ღილაკზე ჩვენება
  const currentLabel = document.getElementById('langCurrentLabel');
  if (currentLabel) currentLabel.textContent = lang.toUpperCase();
}

document.addEventListener('DOMContentLoaded', applyLanguage);
