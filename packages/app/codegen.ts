import type { CodegenConfig } from '@graphql-codegen/cli'

const config: CodegenConfig = {
  overwrite: true,
  schema: process.env.NEXT_PUBLIC_GRAPHQL_ENDPOINT,
  documents: ['**/*.tsx', '**/*.ts'],
  generates: {
    'gql/': {
      preset: 'client',
      plugins: [],
    },
  },
}

export default config
