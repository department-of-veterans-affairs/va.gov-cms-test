/*jshint esversion: 6 */
//

const { ApolloClient, gql } = require('apollo-boost');
const { createHttpLink } = require('apollo-link-http');
const { InMemoryCache } = require('apollo-cache-inmemory');
const fetch = require('node-fetch');


function plugin() {
    const siteUrl = "http://vagovcms.lndo.site/graphql";
    const client = new ApolloClient({
        link: createHttpLink({uri: siteUrl, fetch: fetch}),
        cache: new InMemoryCache()
    });
    const query = gql`
            {
              nodeQuery {
    entities {
      ...on NodePage {
        entityId
        entityUuid
        nid	
        title
        fieldIntroText
        fieldContentBlock {
          entity {
            ... on Paragraph {
              id
              entityRendered
            }
          }
        }
      }
    }
  }
            }
        `;

    const getValueLike = (obj, prop) => {
        let re = new RegExp('^' + prop);
        let value;
        Object.keys(obj).some(function(prop) {
            if (re.test(prop)) {
                value = obj[prop];
                return true;
            }
        });
        return value;
    };

    const getParagraphData = (paragraphField) => {
        let temp = {};
        paragraphField.forEach(function(para){
            let { id, entityRendered} = para.entity;
            temp['paragraph' + id] = entityRendered;
        });
        return temp;
    };

    return function(files, metalsmith, done) {
        const handleSuccess = (resolvedValue) => {
            const entities = resolvedValue.data.nodeQuery.entities;
            let temp = {};
            let paraTemp = {};
            let nodeData = {};
            const values = Object.values(entities);
            values.forEach(function(node) {
                temp = {};
                paraTemp = {};
                let { nid } = node;
                temp.nodeTitle = node.title;
                temp.introText = node.fieldIntroText;

                // Get Paragraph data
                const paragraphs = node.fieldContentBlock;
                paraTemp = getParagraphData(paragraphs);
                nodeData[nid] = Object.assign({}, temp, paraTemp);

            });

            // add nodeData variables to the metalsmith metadata
            let metadata = metalsmith.metadata();
            metadata.nodes = nodeData;
            metalsmith.metadata(metadata);
            console.log(metadata);

            done();
        };

        client.query({
            query: query
        }).then(data => handleSuccess(data))
        .catch(error => console.error(error));
    };
}


// Expose Plugin
module.exports = plugin;