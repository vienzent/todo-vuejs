<template>
    <div class="list-group" >
        <todo-list-item v-for="(item, index) in items" :index="index" :key="index" :item="item" class="item"
                        @itemdeleted="itemDeleted"
                        @itemupdated="itemUpdated"
        />
    </div>
</template>

<script>

import TodoListItem from "./TodoListItem.vue";

export default {
    name: "TodoList",
    props: ['items'],
    components: {
        TodoListItem,
    },
    methods: {
        itemDeleted(item, index) {
            this.$emit('itemdeleted', item, index);
        },
        itemUpdated(item, index) {
            this.$emit('itemupdated', item, index);
        }
    }
}
</script>

<style scoped>
    .list-group {
        margin-top: 20px;
    }
</style>
