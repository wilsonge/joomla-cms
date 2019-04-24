<template>
    <ul class="media-tree" role="group">
        <media-tree-item v-for="(item, index) in directories" :counter="index" :key="item.path" :item="item"
                         :size="directories.length" :level="level" @treeDown="moveFocusDown" @treeUp="moveFocusUp"
                         ref="treeItem">
        </media-tree-item>
    </ul>
</template>

<script>
    export default {
        name: 'media-tree',
        props: {
            'root': {
                type: String,
                required: true
            },
            'level': {
                type: Number,
                required: true
            }
        },
        computed: {
            /* Get the directories */
            directories() {
                return this.$store.state.directories
                    .filter(directory => (directory.directory === this.root))
                    .sort((a, b) => {
                        // Sort alphabetically
                        return (a.name.toUpperCase() < b.name.toUpperCase()) ? -1 : 1
                    });
            },
        },
        methods: {
            /* Handle keydown pressed */
            moveFocusDown(index) {
                // Check if there's another item below the current one
                if ((index +1) >= this.directories.length) {
                    return;
                }

                // Now swap the focus over
                this.$refs['treeItem'].forEach((item) => {
                    if (item.counter === index) {
                        item.removeFocus();
                    }

                    if (item.counter === (index + 1)) {
                        item.focus();
                    }
                });
            },
            /* Handle keydown pressed */
            moveFocusUp(index) {
                // Check if there's another item above the current one
                if ((index - 1) < 0) {
                    return;
                }

                // Now swap the focus over
                this.$refs['treeItem'].forEach((item) => {
                    if (item.counter === index) {
                        item.removeFocus();
                    }

                    if (item.counter === (index - 1)) {
                        item.focus();
                    }
                });
            },
        }
    }
</script>
