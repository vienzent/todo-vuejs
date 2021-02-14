<template>
    <div class="container">
        <div class="row justify-content-center py-5">
            <div class="col-md-8">
                <h2 class="text-center">Todo</h2>
                <todo-add-form @itemadded = "itemAdded"/>
                <todo-list :items="items"
                           @itemdeleted="itemDeleted"
                           @itemupdated="itemUpdated"
                />
            </div>
        </div>
    </div>
</template>

<script>

import TodoList from "./components/TodoList";
import TodoAddForm from "./components/TodoAddForm";

export default {

    name: "App.vue",
    components: {
        TodoList,
        TodoAddForm
    },
    data () {
        return {
            items: [],
        }
    },
    methods: {
        itemDeleted(item, index) {
            this.$delete(this.items, index);
        },
        itemUpdated(item, index) {
            this.$set(this.items, index, item);
        },
        itemAdded(item) {
            this.items.push(item);
        },
        async getList () {
            try {

                let response = await axios.get('api/tasks');

                this.items = response.data.data;

            } catch (e) {
                console.log(e);
            }

        },
    },
    created () {
        this.getList();
    },

}
</script>

<style scoped>

</style>
