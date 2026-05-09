import Box from '@mui/material/Box'
import Button from '@mui/material/Button'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import CardHeader from '@mui/material/CardHeader'
import Chip from '@mui/material/Chip'
import Typography from '@mui/material/Typography'
import { IconPlus } from '@tabler/icons-react'

type CardData = {
  name: string
  cardNumber: string
  expiryDate: string
  cardStatus?: string
  badgeColor?: 'primary' | 'error'
  imgSrc: string
  imgAlt: string
}

const cards: CardData[] = [
  { name: 'Tom McBride', cardNumber: '5577 0000 5577 9865', expiryDate: '12/24', cardStatus: 'Primary', badgeColor: 'primary', imgSrc: '/images/logos/mastercard.png', imgAlt: 'Mastercard' },
  { name: 'Mildred Wagner', cardNumber: '4532 3616 2070 5678', expiryDate: '02/24', imgSrc: '/images/logos/visa.png', imgAlt: 'Visa' },
  { name: 'Lester Jennings', cardNumber: '3700 000000 00002', expiryDate: '08/20', cardStatus: 'Expired', badgeColor: 'error', imgSrc: '/images/logos/american-express.png', imgAlt: 'Amex' },
]

function maskCard(num: string): string {
  return num.slice(0, -4).replace(/[0-9]/g, '*') + num.slice(-4)
}

export default function PaymentMethod() {
  return (
    <Card>
      <CardHeader
        title='Payment Methods'
        action={
          <Button variant='contained' size='small' startIcon={<IconPlus size={16} />}>
            Add Card
          </Button>
        }
      />
      <CardContent sx={{ display: 'flex', flexDirection: 'column', gap: 3 }}>
        {cards.map((card, i) => (
          <Box
            key={i}
            sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', border: 1, borderColor: 'divider', borderRadius: 1, p: 2, flexWrap: 'wrap', gap: 2 }}
          >
            <Box sx={{ display: 'flex', flexDirection: 'column', gap: 1 }}>
              <Box component='img' src={card.imgSrc} alt={card.imgAlt} sx={{ height: 25 }} />
              <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
                <Typography fontWeight={500} color='text.primary'>{card.name}</Typography>
                {card.cardStatus && (
                  <Chip label={card.cardStatus} color={card.badgeColor} size='small' variant='outlined' />
                )}
              </Box>
              <Typography variant='body2'>{maskCard(card.cardNumber)}</Typography>
            </Box>
            <Box sx={{ display: 'flex', flexDirection: 'column', gap: 1.5, alignItems: 'flex-end' }}>
              <Box sx={{ display: 'flex', gap: 1 }}>
                <Button variant='outlined' size='small'>Edit</Button>
                <Button variant='outlined' color='error' size='small'>Delete</Button>
              </Box>
              <Typography variant='body2'>Card expires at {card.expiryDate}</Typography>
            </Box>
          </Box>
        ))}
      </CardContent>
    </Card>
  )
}
