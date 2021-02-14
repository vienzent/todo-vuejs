<template>
<!--    <div class="list-group" >-->
<!--        <todo-list-item v-for="(item, index) in items" :index="index" :key="index" :item="item" class="item"-->
<!--                        @itemdeleted="itemDeleted"-->
<!--                        @itemupdated="itemUpdated"-->
<!--        />-->
<!--    </div>-->
    <draggable  class="list-group" :list="items" group="people" @change="itemSorted">
        <todo-list-item v-for="(item, index) in items" :index="index" :key="item.id" :item="item" class="item"
                        @itemdeleted="itemDeleted"
                        @itemupdated="itemUpdated"
        />
    </draggable>
</template>

<script>

import TodoListItem from "./TodoListItem.vue";
import draggable from "vuedraggable";

export default {
    name: "TodoList",
    props: ['items'],
    components: {
        TodoListItem,
        draggable
    },
    methods: {
        itemDeleted(item, index) {
            this.$emit('itemdeleted', item, index);
        },
        itemUpdated(item, index) {
            this.$emit('itemupdated', item, index);
        },
        async itemSorted(a) {

            if(typeof a.moved === "undefined") return;

            let data = a.moved;

            try {
                let response = await axios.patch(`api/tasks/${data.element.id}/sort`, {old: data.oldIndex, new: data.newIndex})

                console.log(response.data);

                this.$emit('itemsorted', data.element, data.oldIndex, data.newIndex);
            } catch (e) {
                // TODO: cancel sort
                console.log(e);
            }
        },

    }
}
</script>

<style scoped>
    .list-group {
        margin-top: 20px;
    }
</style>
