        @if(isset($dhUsers) && count($dhUsers))
            <?php
                $oldHeader = null;
                $dhUsers = array_reverse($dhUsers);
            ?>
            @foreach($dhUsers as $header => $users)
                <?php
                if ($header !== $oldHeader) {
                    echo sprintf('<h5><strong>%s</strong></h5>', $header);
                    $oldHeader = $header;
                }

                if (count($users)) {
                    foreach ($users as $user) {
                        echo profile($user).'<br />';
                    }
                }

                ?>
            @endforeach
        @endif
