<h1>Feeds</h1>
<table>
    <tr>
        <th>Title</th>
        <th>Description</th>
        <th>Published</th>
    </tr>

    <?php foreach ($feeds as $feed): ?>
        <?php foreach ($feed['Items'] as $item): ?>
        <tr>
            <td><a href="<?php echo $item['link']; ?>"><?php echo $item['title']; ?></a></td>
            <td><?php echo $item['description']; ?></td>
            <td><?php echo $item['published']; ?></td>
        </tr>
        <?php endforeach; ?>
        <?php unset($item); ?>
    <?php endforeach; ?>
    <?php unset($feed); ?>
</table>