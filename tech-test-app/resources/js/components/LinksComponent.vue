<template>
    <div class="container">
      <h1>Link Harvester</h1>
      <h2>Filter Links by Tags</h2>
  
      <div>
        <label v-for="tag in tags" :key="tag">
          <input type="checkbox" :value="tag" v-model="highlightedTags" />
          {{ tag }}
        </label>
        <button @click="getLinks">Show Links</button>
      </div>
  
      <div v-if="links.length">
        <h3>Links:</h3>
        <ul>
          <li v-for="link in links" :key="link.id">
            <a :href="link.url" target="_blank">{{ link.title }}</a> - Tags: {{ link.tags.join(', ') }}
          </li>
        </ul>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        tags: ['php', 'laravel', 'vue', 'api'],
        highlightedTags: [],
        links: []
      };
    },
    methods: {
      async getLinks() {
        try {
          const response = await axios.get('/links', {
            params: { tags: this.highlightedTags.join(',') }
          });
          this.links = response.data;
        } catch (error) {}
      }
    }
  };
  </script>
  
  <style scoped>
  .container {
    max-width: 800px;
    margin: 20px auto;
  }
  
  h1, h2 {
    text-align: center;
  }
  
  button {
    margin-top: 10px;
  }
  </style>
  