<?php

namespace Database\Seeders;

use App\Models\HackerAttack;
use App\Models\News;
use App\Models\CorrelationAnalysis;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ataques Hackers
        $attacks = [
            [
                'title' => 'Critical Ransomware Attack Hits Major Healthcare Provider',
                'description' => 'A sophisticated ransomware gang targets healthcare systems across North America',
                'attack_type' => 'Ransomware',
                'severity' => 'critical',
                'affected_entity' => 'Hospital Network',
                'attack_date' => now()->subDays(2),
                'source_name' => 'CISA',
                'source_url' => 'https://www.cisa.gov/news-events/alerts',
                'tags' => ['ransomware', 'healthcare', 'critical'],
            ],
            [
                'title' => 'New Exploits for Windows Vulnerability CVE-2026-1234',
                'description' => 'Multiple zero-day exploits discovered for recent Windows vulnerability',
                'attack_type' => 'Exploit',
                'severity' => 'high',
                'affected_entity' => 'Microsoft Windows Users',
                'attack_date' => now()->subDays(1),
                'source_name' => 'SecurityAffairs',
                'source_url' => 'https://securityaffairs.com',
                'tags' => ['exploit', 'windows', 'zero-day'],
            ],
            [
                'title' => 'DDoS Attack Disrupts Major E-commerce Platform',
                'description' => 'Large-scale distributed denial of service attack takes down online retailer',
                'attack_type' => 'DDoS',
                'severity' => 'high',
                'affected_entity' => 'E-commerce Platform',
                'attack_date' => now()->subDays(3),
                'source_name' => 'CISA',
                'source_url' => 'https://www.cisa.gov',
                'tags' => ['ddos', 'ecommerce', 'availability'],
            ],
            [
                'title' => 'Phishing Campaign Compromises Financial Institution',
                'description' => 'Targeted phishing emails lead to credential theft in banking sector',
                'attack_type' => 'Phishing',
                'severity' => 'high',
                'affected_entity' => 'Major Bank',
                'attack_date' => now()->subDays(4),
                'source_name' => 'SecurityAffairs',
                'source_url' => 'https://securityaffairs.com',
                'tags' => ['phishing', 'banking', 'credentials'],
            ],
            [
                'title' => 'Data Breach Exposes Millions of Customer Records',
                'description' => 'Hackers steal personal and payment information from global company',
                'attack_type' => 'Data Breach',
                'severity' => 'critical',
                'affected_entity' => 'Tech Company',
                'attack_date' => now()->subDays(5),
                'source_name' => 'CISA',
                'source_url' => 'https://www.cisa.gov',
                'tags' => ['breach', 'data', 'privacy'],
            ],
        ];

        foreach ($attacks as $attack) {
            HackerAttack::create($attack);
        }

        // Notícias
        $news = [
            [
                'title' => 'Healthcare Sector Faces Record Ransomware Attacks in 2026',
                'content' => 'Recent statistics show a significant increase in ransomware attacks targeting healthcare facilities worldwide...',
                'source_name' => 'TechCrunch',
                'source_url' => 'https://techcrunch.com',
                'published_date' => now()->subDays(1),
                'category' => 'Segurança',
                'keywords' => ['ransomware', 'healthcare', 'cybersecurity'],
                'summary' => 'Hospitals and clinics are increasingly becoming targets for ransomware attacks',
                'relevance_score' => 9,
            ],
            [
                'title' => 'Microsoft Releases Emergency Patch for Critical Windows Flaw',
                'content' => 'The software giant issues an out-of-band security update to address critical vulnerability...',
                'source_name' => 'ZDNet',
                'source_url' => 'https://zdnet.com',
                'published_date' => now(),
                'category' => 'Tecnologia',
                'keywords' => ['microsoft', 'windows', 'patch', 'security'],
                'summary' => 'Microsoft releases emergency security patch for critical Windows vulnerability',
                'relevance_score' => 8,
            ],
            [
                'title' => 'Major E-commerce Platform Under DDoS Attack',
                'content' => 'Online retailer experiences service disruption due to distributed denial of service attack...',
                'source_name' => 'TechCrunch',
                'source_url' => 'https://techcrunch.com',
                'published_date' => now()->subDays(2),
                'category' => 'Segurança',
                'keywords' => ['ddos', 'attack', 'ecommerce'],
                'summary' => 'E-commerce giant faces major website downtime from DDoS attack',
                'relevance_score' => 8,
            ],
            [
                'title' => 'Cyber Criminals Steal Customer Data from Banking Institution',
                'content' => 'Phishing campaign successfully compromises financial institution, affecting thousands of customers...',
                'source_name' => 'ZDNet',
                'source_url' => 'https://zdnet.com',
                'published_date' => now()->subDays(3),
                'category' => 'Segurança',
                'keywords' => ['bank', 'fraud', 'phishing', 'credentials'],
                'summary' => 'Phishing attack leads to loss of customer data at major bank',
                'relevance_score' => 9,
            ],
            [
                'title' => 'Tech Company Announces Security Breach Affecting Millions',
                'content' => 'Global technology company confirms data breach exposing customer personal and payment information...',
                'source_name' => 'TechCrunch',
                'source_url' => 'https://techcrunch.com',
                'published_date' => now()->subDays(4),
                'category' => 'Segurança',
                'keywords' => ['breach', 'data', 'privacy', 'leak'],
                'summary' => 'Major data breach exposes millions of customer records',
                'relevance_score' => 10,
            ],
        ];

        foreach ($news as $newsItem) {
            News::create($newsItem);
        }

        // Correlações de Exemplo
        $correlations = [
            [
                'hacker_attack_id' => 1,
                'news_id' => 1,
                'correlation_score' => 95,
                'analysis_reason' => 'Notícia menciona ransomware em healthcare; Ataque também é ransomware em hospital',
                'correlation_type' => 'direct',
                'analysis_date' => now()->toDateString(),
                'is_validated' => true,
            ],
            [
                'hacker_attack_id' => 2,
                'news_id' => 2,
                'correlation_score' => 92,
                'analysis_reason' => 'Ambos mencionam Windows e exploit crítico; Proximidade temporal 1 dia',
                'correlation_type' => 'direct',
                'analysis_date' => now()->toDateString(),
                'is_validated' => true,
            ],
            [
                'hacker_attack_id' => 3,
                'news_id' => 3,
                'correlation_score' => 88,
                'analysis_reason' => 'Ambos descrevem DDoS em plataforma E-commerce; Mesma entidade de negócio',
                'correlation_type' => 'entity_based',
                'analysis_date' => now()->toDateString(),
                'is_validated' => true,
            ],
            [
                'hacker_attack_id' => 4,
                'news_id' => 4,
                'correlation_score' => 91,
                'analysis_reason' => 'Ataque phishing correlacionado com notícia de roubo em banco',
                'correlation_type' => 'direct',
                'analysis_date' => now()->toDateString(),
                'is_validated' => true,
            ],
            [
                'hacker_attack_id' => 5,
                'news_id' => 5,
                'correlation_score' => 97,
                'analysis_reason' => 'Ambos mencionam data breach de tecnologia; Mesma data e company',
                'correlation_type' => 'direct',
                'analysis_date' => now()->toDateString(),
                'is_validated' => true,
            ],
        ];

        foreach ($correlations as $correlation) {
            CorrelationAnalysis::create($correlation);
        }

        $this->command->info('✓ Dados de exemplo carregados com sucesso!');
        $this->command->info('  - ' . HackerAttack::count() . ' Ataques criados');
        $this->command->info('  - ' . News::count() . ' Notícias criadas');
        $this->command->info('  - ' . CorrelationAnalysis::count() . ' Correlações criadas');
    }
}
