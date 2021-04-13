<template>
  <div class="q-pa-md row items-start q-gutter-md">
    <q-card flat bordered style="width: 25vw">
      <q-item>
        <q-item-section>
          <div class="text-h3" v-text="title" />
        </q-item-section>
      </q-item>

      <q-skeleton height="200px" square />
      <q-list>
        <q-item>
          <q-item-section>
            <q-item-label>Brand</q-item-label>
            <q-item-label caption>{{ brand }}</q-item-label>
          </q-item-section>
        </q-item>
      </q-list>
      <q-card-actions class="q-gutter-md justify-between">
        <div class="text-h4">
          &euro;{{ price | monetize }}
          <q-badge
            outline
            align="middle"
            :color="inStock ? 'green' : 'warning'"
          >
            {{ inStock ? "In Stock" : "Out Of Stock" }}
          </q-badge>
        </div>
        <q-btn size="lg" color="primary" label="Add To Chart" />
      </q-card-actions>
    </q-card>
  </div>
</template>

<script>
export default {
  props: {
    product: {
      type: Object,
      default: function () {
        return {
          name: "Title",
          price: 10.0,
          brand: "Brand",
        };
      },
    },
  },
  computed: {
    title() {
      return this.product.name;
    },
    brand() {
      return this.product.brand;
    },
    price() {
      return this.product.price;
    },
    inStock() {
      return !this.product.outOfStock;
    },
  },
};
</script>