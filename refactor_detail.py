import re

with open('pages/news/detail.php', 'r', encoding='utf-8') as f:
    text = f.read()

# Isolate top parts up to <!-- Article -->
top_match = re.search(r'^(.*?)(?=<!-- Article -->)', text, flags=re.DOTALL)
top_part = top_match.group(1) if top_match else ''

header_match = re.search(r'<!-- Breadcrumb -->(.*?)<!-- Featured Media -->', text, re.DOTALL)
header_html = header_match.group(1) if header_match else ''
header_html = header_html.replace(' mb-10 ', ' mb-4 ') # slight margin fix

media_match = re.search(r'<!-- Featured Media -->(.*?)<!-- Content -->', text, re.DOTALL)
media_html = media_match.group(1) if media_match else ''

content_match = re.search(r'<!-- Content -->(.*?)<!-- Share, Comments & Related News -->', text, re.DOTALL)
content_html = content_match.group(1) if content_match else ''
# Remove the closing </div></div></article> from old content
content_html = re.sub(r'</div>\s*</div>\s*</article>', '', content_html, flags=re.DOTALL)

share_match = re.search(r'<!-- Share Buttons -->(.*?)<!-- Comment System Premium -->', text, re.DOTALL)
share_html = share_match.group(1) if share_match else ''

comments_match = re.search(r'<!-- Comment System Premium -->(.*?)<!-- Recommended Content Section \(Figaro Style\) -->', text, re.DOTALL)
comments_html = comments_match.group(1) if comments_match else ''
# Remove the closing section tags
comments_html = re.sub(r'</div>\s*</section>', '', comments_html, flags=re.DOTALL)


left_col = """
<!-- 1. Même catégorie -->
<?php if (!empty($category_news)): ?>
<div class="flex flex-col sticky top-24">
    <h3 class="text-lg font-bold uppercase tracking-widest text-red-700 dark:text-red-500 mb-6 border-b-2 border-red-700 dark:border-red-500 pb-2 text-center">
        Dans la rubrique
    </h3>
    <div class="space-y-6">
        <?php foreach ($category_news as $item): ?>
        <article class="group cursor-pointer flex flex-col border-b border-gray-300 dark:border-gray-800 pb-5 last:border-0 last:pb-0">
            <?php if (!empty($item['embed_code'])): ?>
                <div class="w-full mb-3 h-32 overflow-hidden bg-gray-50 flex justify-center border border-gray-200 dark:border-gray-700">
                    <div class="origin-top scale-75 w-[133%]">
                        <?php echo parseEmbedCode($item['embed_code']); ?>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?php echo getUrl('news', $item['id'], $item['slug']); ?>" class="block w-full mb-3 border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <?php 
                    $item_image = $item['image'] ? SITE_URL . '/uploads/' . $item['image'] : SITE_URL . '/uploads/dk-stade.jpg';
                    ?>
                    <img src="<?php echo $item_image; ?>" 
                         alt="<?php echo htmlspecialchars($item['title']); ?>"
                         class="w-full h-32 object-cover group-hover:scale-105 transition-transform duration-500">
                </a>
            <?php endif; ?>
            
            <div>
                <span class="text-[10px] text-gray-500 font-serif mb-1 block">
                    <?php echo formatDateFR($item['created_at']); ?>
                </span>
                <h4 class="text-base font-bold font-serif leading-tight text-black dark:text-white group-hover:text-red-700 transition-colors mb-2">
                    <a href="<?php echo getUrl('news', $item['id'], $item['slug']); ?>">
                        <?php echo htmlspecialchars($item['title']); ?>
                    </a>
                </h4>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>
"""

right_col = """
<!-- 2. Plus récents -->
<?php if (!empty($recent_news)): ?>
<div class="flex flex-col sticky top-24">
    <h3 class="text-lg font-bold uppercase tracking-widest text-red-700 dark:text-red-500 mb-6 border-b-2 border-red-700 dark:border-red-500 pb-2 text-center">
        Le Fil Infos
    </h3>
    <div class="space-y-4">
        <?php foreach ($recent_news as $item): ?>
        <article class="group cursor-pointer flex flex-col border-b border-gray-300 dark:border-gray-800 pb-4 last:border-0 last:pb-0">
            <div>
                <span class="text-[10px] text-gray-500 font-serif mb-1 block">
                    <?php echo date('H:i', strtotime($item['created_at'])); ?>
                </span>
                <h4 class="text-base font-bold font-serif leading-tight text-black dark:text-white group-hover:text-red-700 transition-colors">
                    <a href="<?php echo getUrl('news', $item['id'], $item['slug']); ?>">
                        <?php echo htmlspecialchars($item['title']); ?>
                    </a>
                </h4>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>
"""

bottom_col = """
<!-- 3. Plus lus -->
<section class="py-16 bg-white dark:bg-slate-900 border-t-[3px] border-black dark:border-white transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if (!empty($popular_news)): ?>
        <h2 class="text-3xl font-black uppercase tracking-widest font-serif text-black dark:text-white mb-10 text-center border-b border-gray-300 dark:border-gray-800 pb-4">Les Plus Lus</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php foreach ($popular_news as $index => $item): ?>
            <article class="group cursor-pointer flex flex-col border-b border-gray-300 dark:border-gray-800 pb-6 lg:border-b-0 lg:pb-0 lg:border-r last:border-0 lg:pr-8 last:pr-0 items-start">
                <div class="text-5xl font-black font-serif text-gray-200 dark:text-slate-800 mb-2">
                    <?php echo $index + 1; ?>
                </div>
                <div class="flex-1 w-full">
                    <h4 class="text-lg font-bold font-serif leading-tight text-black dark:text-white group-hover:text-red-700 dark:group-hover:text-red-500 transition-colors mb-2">
                        <a href="<?php echo getUrl('news', $item['id'], $item['slug']); ?>">
                            <?php echo htmlspecialchars($item['title']); ?>
                        </a>
                    </h4>
                    <span class="text-[10px] text-gray-500 font-serif block mt-2">
                        <?php echo $item['views']; ?> lectures
                    </span>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
"""

bottom_match = re.search(r'(<script>.*?)$', text, re.DOTALL)
bottom_js = bottom_match.group(1) if bottom_match else ''


new_html = top_part + """<!-- Article -->
<article class="py-12 bg-white dark:bg-slate-900 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- HEADER ARTICLE (Center aligned, max width) -->
        <header class="max-w-4xl mx-auto mb-10 border-b border-gray-300 dark:border-gray-800 pb-4">
            <!-- Breadcrumb -->
            """ + header_html + """
        </header>

        <div class="flex flex-col lg:flex-row gap-8 lg:gap-12">
            
            <!-- LEFT COLUMN: Même rubrique -->
            <aside class="w-full lg:w-3/12 order-2 lg:order-1 lg:pr-6 lg:border-r border-gray-300 dark:border-gray-800">
                """ + left_col + """
            </aside>

            <!-- CENTER COLUMN: Article & Comments -->
            <main class="w-full lg:w-6/12 order-1 lg:order-2">
                <!-- Featured Media -->
                """ + media_html + """
                
                <!-- Content -->
                """ + content_html + """

                <!-- Share Buttons -->
                """ + share_html + """

                <!-- Comment System -->
                """ + comments_html + """
            </main>

            <!-- RIGHT COLUMN: Le Fil Infos -->
            <aside class="w-full lg:w-3/12 order-3 lg:pl-6 lg:border-l border-gray-300 dark:border-gray-800">
                """ + right_col + """
            </aside>

        </div>
    </div>
</article>

""" + bottom_col + """

""" + bottom_js

import hashlib

with open('pages/news/detail.php', 'w', encoding='utf-8') as f:
    f.write(new_html)

