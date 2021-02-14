<template>
    <form @submit.prevent="addItem">
        <div class="input-group">
            <input v-model="item.description" type="text" class="form-control" placeholder="Enter your task...">
            <div class="input-group-append">
                <button type="submit" class="btn btn-primary">
                    <font-awesome-icon icon="plus" />
                </button>
            </div>
        </div>
    </form>
</template>

<script>

export default {
    name: "TodoAddForm",
    data() {
        return {
            item: {
                description: null,
            }
        }
    },
    methods: {
        async addItem() {

            try {
                let response = await axios.post('api/tasks', this.item);
                console.log(response);

                this.item.description = null;

                this.$emit('itemadded', response.data.data);


            } catch (e) {
                console.log(e);
                // TODO: add error message
            }

        },
    },

}
</script>

<style scoped>
input {

}
</style>
