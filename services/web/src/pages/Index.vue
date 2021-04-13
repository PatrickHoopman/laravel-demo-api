<template>
  <q-page class="flex justify-center">
    <product-card
      v-for="product in products"
      :product="product"
      :key="product.serial"
    />
  </q-page>
</template>

<script>
import ProductCard from "../components/ProductCard";
export default {
  components: { ProductCard },
  name: "PageIndex",
  data() {
    return {
      products: [],
    };
  },
  methods: {
    async fetchProducts() {
      const {
        data: { products },
      } = await this.$api.get("/products");
      this.products = products;
    },
  },
  mounted() {
    this.fetchProducts();
  },
};
</script>
