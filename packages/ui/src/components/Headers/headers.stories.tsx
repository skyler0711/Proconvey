import { ComponentMeta } from '@storybook/react'
import { H1, H2, H3, H4 } from './index'

const storyBooks = [
  {
    title: 'Components/H1',
    component: H1,
  } as ComponentMeta<typeof H1>,
  {
    title: 'Components/H2',
    component: H2,
  } as ComponentMeta<typeof H2>,
]

export default storyBooks

export const Heading1 = (): JSX.Element => {
  return (
    <H1>Heading 1</H1>
  )
}

export const Heading2 = (): JSX.Element => {
  return (
    <H2>Heading 2</H2>
  )
}

export const Heading3 = (): JSX.Element => {
  return (
    <H3>Heading 3</H3>
  )
}

export const Heading4 = (): JSX.Element => {
  return (
    <H4>Heading 4</H4>
  )
}
