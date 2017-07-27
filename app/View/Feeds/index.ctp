<div>
<?php foreach ($categories as $cat): ?>
    <a href="<?php echo '/?category=' . $cat; ?>">
        <?php echo $cat ?>
    </a>
<?php endforeach; ?>
</div>
<table>
    <tr>
        <th>Title</th>
        <?php if(!isset($category)): ?>
            <th>Category</th>
        <?php endif; ?>
        <th>Updated</th>
        <th>Articles Count</th>
        <th>Recent Article</th>
    </tr>

    <?php foreach ($feeds as $feed): ?>
        <tr>
            <td><a href="<?php echo $feed['Feed']['url']; ?>"><?php echo $feed['Feed']['title']; ?></a></td>
            <?php if(!isset($category)): ?>
                <td><?php echo $feed['Feed']['category']; ?></td>
            <?php endif; ?>
            <td><?php echo $feed['Feed']['last_update']; ?></td>
            <td><?php echo count($feed['Items']) ?></td>
            <td>
                <?php if (array_key_exists('recentArticle', $feed['Feed'])): ?>
                    <a href="<?php echo $feed['Feed']['recentArticle']['link'] ?>">
                        <?php echo $feed['Feed']['recentArticle']['title'] ?>
                    </a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php unset($feed); ?>
</table>